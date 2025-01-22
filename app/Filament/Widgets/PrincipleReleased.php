<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\LineChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class PrincipleReleased extends LineChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
   
    protected static ?string $maxHeight = '200px';
    protected static ?int $sort = 2;

   



    public function getHeading(): string
    {
        return 'Funds Disbursed';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $records = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $records[] = Loan::query()
            ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            // ->where('loan_status', 'approved')

            ->whereMonth('created_at', $month)
            ->sum('principal_amount');
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Total principle released',
                    'data' => array_map('floatval', $records),
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
        


    }

}