<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | UnitEnum | null $navigationGroup = 'Katalog & Inventori';

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori Produk';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori Produk')
                    ->description('Kelompokkan produk ke dalam kategori agar mudah dicari pelanggan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Pakaian & Seragam')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),

                        TextInput::make('slug')
                            ->label('Slug URL')
                            ->placeholder('pakaian-seragam')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Jumlah Produk')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Kategori'),
            ])
            ->recordActions([
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

