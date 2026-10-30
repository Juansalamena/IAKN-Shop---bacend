<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Gerai')
                ->visible(fn (): bool => User::current()?->isSuperAdmin() ?? false),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

