<?php

namespace App\Providers\Filament;

use App\Filament\Resources\LoanResource;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

          ->navigationItems([
            NavigationItem::make('Active Loans')
                ->url('active')
                ->icon('fas-coins')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.loans.active'))
                ->sort(4),
                
                
          ])
          ->navigationItems([
            NavigationItem::make('Pending Loans')
                ->url('pending')
                ->icon('fas-clock')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.pending-loans'))
                ->sort(3),
                
                
          ])
          ->navigationItems([
            NavigationItem::make('Denied Loans')
                ->url('denied')
                ->icon('fas-ban')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.denied-loans'))
                ->sort(5),
          ])
          ->navigationItems([
            NavigationItem::make('Defalted Loans')
                ->url('defaulted')
                ->icon('fas-exclamation-triangle')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.defaulted-loans'))
                ->sort(6),
          ])
          
        ->
          default()
            ->id('admin')
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
