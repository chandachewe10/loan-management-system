<?php

namespace App\Providers\Filament;
use Filament\Navigation\MenuItem;
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
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Rmsramos\Activitylog\ActivitylogPlugin;
use App\Http\Middleware\CheckSubscriptionValidity;
use App\Filament\Pages\Auth\Register;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
        ->id('admin')
        ->path('admin')
        ->plugins([
            FilamentShieldPlugin::make()
                ->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 2
                ])
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 4,
                ])
                ->resourceCheckboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                ]),
                ActivitylogPlugin::make()
                ->authorize(
                    fn () => auth()->user()->hasRole('super_admin')
                ),
        ])
        // ->brandLogo(asset('Logos/logo2.png'))
        // ->brandLogoHeight('4rem')
        // ->favicon(asset('Logos/logo2.png'))
        ->sidebarCollapsibleOnDesktop()

        ->login()
        ->registration(Register::class)
        ->passwordReset()
        ->emailVerification()
        ->profile()
        ->default()
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
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Statement of Financial Position')
                    ->url('/admin/assets/statement-of-financial-position')
                    ->icon('heroicon-m-banknotes')
                    ->group('Accounting')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.assets.statement'))
                    ->sort(4),
                NavigationItem::make('Statement of Comprehensive Income')
                    ->url('/admin/assets/statement-of-comprehensive-income')
                    ->icon('heroicon-m-chart-bar')
                    ->group('Accounting')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.assets.comprehensive_income'))
                    ->sort(5),
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
             //   CheckSubscriptionValidity::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
