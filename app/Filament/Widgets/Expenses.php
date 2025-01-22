<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\LineChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Models\Expense;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class Expenses extends LineChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;


    protected static ?int $sort = 3;





    public function getHeading(): string
    {
        return 'Business Expenses';
    }

    protected function getData(): array
{
    $startDate = $this->filters['startDate'] ?? null;
    $endDate = $this->filters['endDate'] ?? null;
    $records = [];

    for ($month = 1; $month <= 12; $month++) {
        $records[] = Expense::query()
            ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->whereMonth('created_at', $month)
            ->sum('expense_amount');
    }

    // Multiply each value in $records by -1
   // $records = array_map(fn($value) => $value * (-1), $records);
   $records = array_map(fn($value) => $value, $records);

    return [
        'datasets' => [
            [
                'label' => 'Business Expenses',
                'data' => array_map('floatval', $records),
            ],
        ],
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    ];
}


}