<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OwnerPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            function (): string {
                if (!app('impersonate')->isImpersonating()) {
                    return '';
                }

                $userName = auth()->user()?->name ?? 'este usuario';
                $leaveUrl = route('impersonate.leave');

                return <<<HTML
                <div style="background:#D4AF37;color:#0B0B0B;padding:10px 16px;text-align:center;font-weight:600;font-size:14px;z-index:9999;position:relative;">
                    ⚠️ Modo soporte activo — viendo el panel como <strong>{$userName}</strong>
                    &nbsp;|&nbsp;
                    <a href="{$leaveUrl}" style="text-decoration:underline;color:#0B0B0B;">Salir y volver a admin</a>
                </div>
                HTML;
            },
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('owner')
            ->path('owner')
            ->login()
            ->brandName('Dashboard de Dueño')
            ->brandLogo(fn () => view('filament.owner.brand-logo'))
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Red,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Inter')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Owner/Resources'), for: 'App\\Filament\\Owner\\Resources')
            ->discoverPages(in: app_path('Filament/Owner/Pages'), for: 'App\\Filament\\Owner\\Pages')
            ->pages([
                \App\Filament\Owner\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Owner/Widgets'), for: 'App\\Filament\\Owner\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                \App\Http\Middleware\CheckOwnerAccess::class,
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
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->navigationGroups([
                'Mi Negocio',
                'Contenido',
                'Estadísticas',
                'Configuración',
            ]);
    }
}
