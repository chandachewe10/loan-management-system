<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\LineChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;


class Expenses extends LineChartWidget
{
    use InteractsWithPageFilters;


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
        $records[] = \Bavix\Wallet\Models\Transaction::query()
            ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->where('type', '=', 'withdraw')
            ->whereMonth('created_at', $month)
            ->sum('amount');
    }

    // Multiply each value in $records by -1
    $records = array_map(fn($value) => $value * -1, $records);

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