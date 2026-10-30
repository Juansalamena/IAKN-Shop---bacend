<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Gerai & Pengguna';

    protected static ?string $navigationLabel = 'Profil & Cabang Gerai';

    protected static ?string $modelLabel = 'Gerai';

    protected static ?string $pluralModelLabel = 'Data Gerai / Cabang';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withCount(['products', 'orders']);
        $user = User::current();

        if ($user && $user->isStoreAdmin()) {
            $query->where('id', $user->store_id);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return User::current()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return User::current()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil & Informasi Operasional Gerai')
                    ->description('Kelola identitas gerai, kontak layanan pelanggan, alamat cabang, dan status operasional.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Gerai / Cabang')
                            ->placeholder('Contoh: IAKN Shop - Gerai Ambon')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, ?Model $record) => $record === null ? $set('slug', Str::slug((string) $state)) : null),

                        TextInput::make('slug')
                            ->label('Slug Identitas Gerai')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp Gerai')
                            ->placeholder('0821xxxxxxxx')
                            ->maxLength(25),

                        Toggle::make('is_active')
                            ->label('Gerai Buka / Aktif Beroperasi')
                            ->default(true),

                        Textarea::make('address')
                            ->label('Alamat Lengkap Gerai')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Gerai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Store $record): string => $record->address ?? '-'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->searchable()
                    ->default('-'),

                TextColumn::make('products_count')
                    ->label('Produk')
                    ->badge()
                    ->color('info'),

                TextColumn::make('orders_count')
                    ->label('Total Pesanan')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Gerai Baru')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit Profil Gerai'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}

