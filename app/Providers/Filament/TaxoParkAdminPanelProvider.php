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
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Auth\Login;

class TaxoParkAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('taxoParkAdmin')
            ->path('taxoParkAdmin')
            ->colors([
                // 'primary' => Color::Amber,
                'primary' => "white" // to'g'rilash k-k
            ])
            ->discoverResources(in: app_path('Filament/TaxoParkAdmin/Resources'), for: 'App\Filament\TaxoParkAdmin\Resources')
            ->discoverPages(in: app_path('Filament/TaxoParkAdmin/Pages'), for: 'App\Filament\TaxoParkAdmin\Pages')
            ->pages([
                Dashboard::class,
            ])->login(Login::class)
            ->discoverWidgets(in: app_path('Filament/TaxoParkAdmin/Widgets'), for: 'App\Filament\TaxoParkAdmin\Widgets')
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
