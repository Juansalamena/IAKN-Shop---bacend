<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;

class AuraDashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Aura Store';

    protected string $view = 'filament.pages.aura-dashboard';

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public function getHeading(): string
    {
        return '';
    }
}
