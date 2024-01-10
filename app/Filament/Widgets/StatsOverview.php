<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StatsOverviewResource\Widgets\AdminChart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class StatsOverview extends BaseWidget
{
    protected static ?string $maxHeight = '100px';
    protected static ?int $sort = 2;
    public function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Active Loans', \App\Models\Loan::where('loan_status', 'approved')->count())
                ->description('Active Loans')
                ->descriptionIcon('fas-wallet')
                ->color('info'),

            Stat::make('Pending Loans', \App\Models\Loan::where('loan_status', 'processing')->count())
                ->description('Pending Loans')
                ->descriptionIcon('fas-clock')
                ->color('primary'),
            Stat::make('Defaulted Loans', \App\Models\Loan::where('loan_status', 'defaulted')->count())
                ->description('Defaulted Loans')
                ->descriptionIcon('fas-dollar-sign')
                ->color('danger'),


            Stat::make('Fully Paid Loans', \App\Models\Loan::where('loan_status', 'fully_paid')->count())
                ->description('Fully Paid Loans')
                ->descriptionIcon('fas-wallet')
                ->color('success')










        ];
    }
}
