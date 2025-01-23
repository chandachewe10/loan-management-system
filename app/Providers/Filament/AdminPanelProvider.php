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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
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
        ])
        // ->brandLogo(asset('Logos/logo2.png'))
        // ->brandLogoHeight('4rem')
        // ->favicon(asset('Logos/logo2.png'))
        ->sidebarCollapsibleOnDesktop()
        
        ->login()
        ->registration()
        ->passwordReset()
        ->emailVerification()
        ->profile()
          ->navigationItems([
            NavigationItem::make('Active Loans')            
                ->url('/admin/loans/active')
                ->icon('fas-coins')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.loans.active'))
                // ->badge(\App\Models\Loan::where('loan_status',"=",'approved')->count(), 'success')
                ->sort(4),
                
                
          ])
          ->navigationItems([
            NavigationItem::make('Pending Loans')
                ->url('/admin/loans/pending')
                ->icon('fas-clock')
                ->group('Loans')
                // ->badge(\App\Models\Loan::where('loan_status',"=",'processing')->count(), 'info')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.pending-loans'))
                ->sort(3),
                
                
          ])
          ->navigationItems([
            NavigationItem::make('Denied Loans')
                ->url('/admin/loans/denied')
                ->icon('fas-ban')
                ->group('Loans')
                // ->badge(\App\Models\Loan::where('loan_status',"=",'denied')->count(), 'danger')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.denied-loans'))
                ->sort(5),
          ])
          ->
          navigationItems([
              NavigationItem::make('Partially Paid Loans')
                  ->url('/admin/loans/partially_paid')
                  ->icon('fas-adjust')
                //   ->badge(\App\Models\Loan::where('loan_status',"=",'partially_paid')->count(), 'primary')
                  ->group('Loans')
                  ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.partially-loans'))
                  ->sort(6),
            ])
            ->
            navigationItems([
                NavigationItem::make('Fully Paid Loans')
                    ->url('/admin/loans/fully_paid')
                    ->icon('fas-square-full')
                    // ->badge(\App\Models\Loan::where('loan_status',"=",'fully_paid')->count(), 'success')
                    ->group('Loans')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.fully-loans'))
                    ->sort(7),
              ])
          ->navigationItems([
            NavigationItem::make('Defaulted Loans')
                ->url('/admin/loans/defaulted')
                ->icon('fas-exclamation-triangle')
                // ->badge(\App\Models\Loan::where('loan_status',"=",'defaulted')->count(), 'warning')
                ->group('Loans')
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.loan-resource.pages.defaulted-loans'))
                ->sort(8),
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
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
