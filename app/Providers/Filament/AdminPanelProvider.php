<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Pages;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Panel;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\PanelProvider;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Filament\Widgets;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Session\Middleware\StartSession;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->renderHook(
                'panels::body.end',
                fn () => new HtmlString('<script src="/fix-widget.js"></script>')
            )
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
