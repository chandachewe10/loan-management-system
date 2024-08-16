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


class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
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
                ->count())
                ->description('Active Loans')
                ->descriptionIcon('fas-wallet')
                ->color('info'),

            Stat::make('Pending Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'processing')
                ->count(), )
                ->description('Pending Loans')
                ->descriptionIcon('fas-clock')
                ->color('primary'),
            Stat::make('Defaulted Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'defaulted')
                ->count(), )
                ->description('Defaulted Loans')
                ->descriptionIcon('fas-sync')
                ->color('danger'),


            Stat::make('Fully Paid Loans', Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->where('loan_status', 'fully_paid')
                ->count())
                ->description('Fully Paid Loans')
                ->descriptionIcon('fas-wallet')
                ->color('success')










        ];
    }
}
