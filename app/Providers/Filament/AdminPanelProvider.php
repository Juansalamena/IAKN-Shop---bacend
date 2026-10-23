<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Aura Store')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString('
                    <style>
                        .fi-simple-layout {
                            background: radial-gradient(ellipse at 50% 0%, #1e1b4b 0%, #0f172a 65%, #020617 100%) !important;
                            position: relative;
                            min-height: 100vh;
                        }
                        .fi-simple-layout::before {
                            content: "";
                            position: absolute;
                            inset: 0;
                            background-image: radial-gradient(rgba(129, 140, 248, 0.12) 1px, transparent 1px);
                            background-size: 28px 28px;
                            pointer-events: none;
                        }
                        .fi-simple-main {
                            background: rgba(15, 23, 42, 0.8) !important;
                            backdrop-filter: blur(20px) !important;
                            -webkit-backdrop-filter: blur(20px) !important;
                            border: 1px solid rgba(255, 255, 255, 0.1) !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(99, 102, 241, 0.15) !important;
                            position: relative;
                            z-index: 10;
                        }
                        .fi-simple-header-heading {
                            font-weight: 700 !important;
                            letter-spacing: -0.025em !important;
                        }
                        .fi-logo {
                            font-weight: 800 !important;
                            letter-spacing: -0.02em !important;
                            font-size: 1.6rem !important;
                            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Pages\AuraDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
