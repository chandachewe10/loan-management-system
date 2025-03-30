<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StatsOverviewResource\Widgets\AdminChart;
use App\Models\Loan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    protected static ?string $maxHeight = '100px';
    protected static ?int $sort = 1;
    public function getColumns(): int
    {
        return 2;
    }


    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        return [


            Stat::make('Active Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'approved')
                ->orWhere('loan_status', 'partially_paid')
                ->count())
                ->description('Active Loans')
                ->descriptionIcon('fas-wallet')
                ->color('info')
                ->url('admin/loans'),

            Stat::make('Pending Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'processing')
                ->count(), )
                ->description('Pending Loans')
                ->descriptionIcon('fas-clock')
                ->color('primary')
                ->url('admin/loans?activeTab=Processing'),

            Stat::make('Defaulted Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'defaulted')
                ->count(), )
                ->description('Defaulted Loans')
                ->descriptionIcon('fas-sync')
                ->color('danger')
                ->url('admin/loans?activeTab=Over+Due'),


            Stat::make('Fully Paid Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'fully_paid')
                ->count())
                ->description('Fully Paid Loans')
                ->descriptionIcon('fas-wallet')
                ->color('success')
                ->url('admin/loans?activeTab=Settled')










        ];
    }
}
