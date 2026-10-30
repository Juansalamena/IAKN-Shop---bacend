<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | UnitEnum | null $navigationGroup = 'Transaksi & Penjualan';

    protected static ?string $navigationLabel = 'Pesanan Masuk';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Manajemen Pesanan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = (clone static::getEloquentQuery())
            ->where('status', Order::STATUS_PENDING)
            ->count();

        if ($pendingCount > 0) {
            return "{$pendingCount} Baru";
        }

        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        $pendingCount = (clone static::getEloquentQuery())
            ->where('status', Order::STATUS_PENDING)
            ->count();

        return $pendingCount > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['customer', 'store', 'items']);
        $user = User::current();

        if ($user && $user->isStoreAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $user = User::current();
        $isStoreAdmin = $user && $user->isStoreAdmin();

        return $schema
            ->components([
                Section::make('Informasi Transaksi Pesanan')
                    ->description('Detail pelanggan, gerai tujuan, status pemrosesan, dan alamat pengiriman.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Nomor Pesanan')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Dibuat otomatis saat disimpan'),

                        $isStoreAdmin
                            ? Hidden::make('store_id')
                                ->default(fn () => User::current()?->store_id)
                                ->dehydrated(true)
                            : Select::make('store_id')
                                ->label('Gerai / Cabang')
                                ->relationship('store', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                        Select::make('customer_id')
                            ->label('Pelanggan (Customer)')
                            ->relationship(
                                name: 'customer',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('role', 'customer'),
                            )
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                Order::STATUS_PENDING => 'PENDING (Menunggu Konfirmasi)',
                                Order::STATUS_PROCESSED => 'PROCESSED (Sedang Diproses)',
                                Order::STATUS_SHIPPED => 'SHIPPED (Dalam Pengiriman)',
                                Order::STATUS_DELIVERED => 'DELIVERED (Selesai / Diterima)',
                                Order::STATUS_CANCELLED => 'CANCELLED (Dibatalkan)',
                            ])
                            ->default(Order::STATUS_PENDING)
                            ->required(),

                        TextInput::make('total_amount')
                            ->label('Total Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required(),

                        Textarea::make('shipping_address')
                            ->label('Alamat Pengiriman Lengkap')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan Pembeli / Pengiriman')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Daftar Item Produk yang Dipesan')
                    ->description('Rincian produk dalam pesanan ini.')
                    ->schema([
                        Repeater::make('items')
                            ->label('Item Pesanan')
                            ->relationship('items')
                            ->columns(4)
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->options(function () use ($isStoreAdmin, $user) {
                                        $query = Product::query();
                                        if ($isStoreAdmin && $user?->store_id) {
                                            $query->where('store_id', $user->store_id);
                                        }
                                        return $query->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $qty = max(1, (int) ($get('quantity') ?? 1));
                                            $set('product_name', $product->name);
                                            $set('price', (float) $product->price);
                                            $set('subtotal', (float) $product->price * $qty);
                                        }
                                    }),

                                Hidden::make('product_name')
                                    ->dehydrated(true),

                                TextInput::make('price')
                                    ->label('Harga Satuan (Rp)')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $qty = max(1, (int) ($get('quantity') ?? 1));
                                        $set('subtotal', (float) $state * $qty);
                                    }),

                                TextInput::make('quantity')
                                    ->label('Jumlah (Qty)')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $price = (float) ($get('price') ?? 0);
                                        $set('subtotal', $price * max(1, (int) $state));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal (Rp)')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->defaultItems(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record): string => $record->customer?->phone ?? $record->customer?->email ?? '-'),

                TextColumn::make('store.name')
                    ->label('Gerai')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Jml Item')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('total_amount')
                    ->label('Total Belanja')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PENDING => 'warning',
                        Order::STATUS_PROCESSED => 'info',
                        Order::STATUS_SHIPPED => 'primary',
                        Order::STATUS_DELIVERED => 'success',
                        Order::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('shipping_address')
                    ->label('Alamat Pengiriman')
                    ->limit(35)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Waktu Order')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options([
                        Order::STATUS_PENDING => 'PENDING',
                        Order::STATUS_PROCESSED => 'PROCESSED',
                        Order::STATUS_SHIPPED => 'SHIPPED',
                        Order::STATUS_DELIVERED => 'DELIVERED',
                        Order::STATUS_CANCELLED => 'CANCELLED',
                    ]),

                SelectFilter::make('store_id')
                    ->label('Gerai')
                    ->relationship('store', 'name')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Pesanan Manual'),
            ])
            ->recordActions([
                Action::make('updateStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->fillForm(fn (Order $record): array => [
                        'status' => $record->status,
                        'notes' => $record->notes,
                    ])
                    ->schema([
                        Select::make('status')
                            ->label('Status Pesanan Baru')
                            ->options([
                                Order::STATUS_PENDING => 'PENDING (Menunggu Konfirmasi)',
                                Order::STATUS_PROCESSED => 'PROCESSED (Sedang Diproses / Dikemas)',
                                Order::STATUS_SHIPPED => 'SHIPPED (Dalam Pengiriman Kurir)',
                                Order::STATUS_DELIVERED => 'DELIVERED (Pesanan Selesai Diterima)',
                                Order::STATUS_CANCELLED => 'CANCELLED (Batalkan & Kembalikan Stok)',
                            ])
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan / No. Resi Pengiriman')
                            ->rows(2),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $oldStatus = $record->status;
                        $newStatus = $data['status'];

                        if ($newStatus === Order::STATUS_CANCELLED && $oldStatus !== Order::STATUS_CANCELLED) {
                            foreach ($record->items as $item) {
                                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                            }
                        }

                        $record->update([
                            'status' => $newStatus,
                            'notes' => $data['notes'] ?? $record->notes,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Status pesanan diperbarui')
                            ->body("Pesanan {$record->order_number} diubah dari {$oldStatus} menjadi {$newStatus}.")
                            ->send();
                    }),
                EditAction::make()
                    ->label('Detail / Edit'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

