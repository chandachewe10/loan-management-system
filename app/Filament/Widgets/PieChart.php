<?php

namespace App\Filament\Widgets;

use App\Models\Repayments;
use Filament\Widgets\ChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class PieChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Total Collected';
    protected static ?string $maxHeight = '200px';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $records = [];
        for ($month = 1; $month <= 12; $month++) {
            $records[] = Repayments::query()
            ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->whereMonth('created_at', $month)
            ->sum('payments');
        }
        

        return [
            'datasets' => [
                [
                    'data' => array_map('floatval', $records),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4CAF50',
                        '#FF8C00',
                        '#9966CC',
                        '#00BFFF',
                        '#FFD700',
                        '#008080',
                        '#FF4500',
                        '#8A2BE2',
                        '#1E90FF',
                    ],
                    'hoverBackgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4CAF50',
                        '#FF8C00',
                        '#9966CC',
                        '#00BFFF',
                        '#FFD700',
                        '#008080',
                        '#FF4500',
                        '#8A2BE2',
                        '#1E90FF',
                    ],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Use 'pie' for a pie chart
    }
}
