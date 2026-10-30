<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string | UnitEnum | null $navigationGroup = 'Katalog & Inventori';

    protected static ?string $navigationLabel = 'Produk & Stok';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk & Stok Gerai';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        $lowStockCount = (clone static::getEloquentQuery())->where('stock', '<=', 5)->count();

        return $lowStockCount > 0 ? 'warning' : 'primary';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['store', 'category']);
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
                Section::make('Informasi Produk Gerai')
                    ->description('Kelola data produk, harga, stok inventori, dan kategori untuk gerai Anda.')
                    ->columns(2)
                    ->schema([
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

                        Select::make('category_id')
                            ->label('Kategori Produk')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->placeholder('Contoh: Jaket Almamater IAKN')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('sku')
                            ->label('Kode SKU')
                            ->placeholder('Contoh: AMB-ALM-001')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->default(0),

                        TextInput::make('stock')
                            ->label('Stok Tersedia')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(10),

                        TextInput::make('image')
                            ->label('URL Gambar Produk')
                            ->placeholder('https://images.unsplash.com/...')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->placeholder('Tuliskan deskripsi lengkap produk...')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Status Produk Aktif (Tampil di Katalog Pelanggan)')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular()
                    ->defaultImageUrl('https://placehold.co/100x100/151926/ffffff?text=Prod'),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record): string => Str::limit((string) $record->description, 45)),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('store.name')
                    ->label('Gerai')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->default('Tanpa Kategori')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Habis (0)' : "{$state} unit")
                    ->color(fn (int $state): string => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('store_id')
                    ->label('Gerai')
                    ->relationship('store', 'name')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),

                TernaryFilter::make('is_active')
                    ->label('Status Katalog Aktif'),

                Filter::make('low_stock')
                    ->label('Stok Menipis (<= 10)')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '<=', 10)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Produk'),
            ])
            ->recordActions([
                Action::make('updateStock')
                    ->label('Stok')
                    ->icon('heroicon-o-cube')
                    ->color('warning')
                    ->fillForm(fn (Product $record): array => [
                        'stock' => $record->stock,
                        'is_active' => $record->is_active,
                    ])
                    ->schema([
                        TextInput::make('stock')
                            ->label('Jumlah Stok Baru')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        Toggle::make('is_active')
                            ->label('Tampilkan di Katalog Aktif'),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->update([
                            'stock' => (int) $data['stock'],
                            'is_active' => (bool) $data['is_active'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Stok produk berhasil diperbarui')
                            ->body("Stok {$record->name} kini {$record->stock} unit.")
                            ->send();
                    }),
                EditAction::make()
                    ->label('Edit'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
