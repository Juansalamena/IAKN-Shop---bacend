<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Gerai & Pengguna';

    protected static ?string $navigationLabel = 'Pelanggan & Admin';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Data Pelanggan & Pengguna';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['store'])->withCount('orders');
        $user = User::current();

        if ($user && $user->isStoreAdmin()) {
            $query->where('role', 'customer');
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return User::current()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
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
                Section::make('Informasi Akun Pengguna')
                    ->description('Kelola akun Super Admin, Admin Gerai (Cabang), maupun Pelanggan (Customer).')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->label('Peran (Role)')
                            ->options([
                                'customer' => 'Customer (Pelanggan)',
                                'store_admin' => 'Store Admin (Admin Gerai)',
                                'super_admin' => 'Super Admin (Pusat)',
                            ])
                            ->default('customer')
                            ->required()
                            ->live(),

                        Select::make('store_id')
                            ->label('Penugasan Gerai (Khusus Admin Gerai)')
                            ->relationship('store', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get): bool => $get('role') === 'store_admin')
                            ->required(fn (callable $get): bool => $get('role') === 'store_admin'),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->maxLength(25),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->placeholder('Kosongkan jika tidak ingin mengubah password'),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'store_admin' => 'Admin Gerai',
                        'customer' => 'Customer',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'store_admin' => 'primary',
                        'customer' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('store.name')
                    ->label('Gerai')
                    ->default('-')
                    ->badge()
                    ->color('info')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->default('-')
                    ->searchable(),

                TextColumn::make('orders_count')
                    ->label('Total Order')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'customer' => 'Customer',
                        'store_admin' => 'Admin Gerai',
                        'super_admin' => 'Super Admin',
                    ])
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Pengguna')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

