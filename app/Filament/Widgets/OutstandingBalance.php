<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LoanResource;
use App\Models\Loan;
use App\Models\Repayments;
use Filament\Widgets\ChartWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class OutstandingBalance extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Outstanding Balance';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $records = [];
        $labels = [];

        // Assuming you have a specific time range, adjust as needed
        $startMonth = 1;
        $endMonth = CarbonImmutable::now()->month;

        for ($month = $startMonth; $month <= $endMonth; $month++) {
            $labels[] = CarbonImmutable::create(null, $month, 1)->format('M');
            $records[] = Loan::query()
                ->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->whereMonth('created_at', $month)
                ->where('loan_status', "=", 'approved')
                ->sum('balance');
        }

        return [
            'datasets' => [
                [
                    'data' => array_map('floatval', $records),
                    'label' => 'Outstanding Balance',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Use 'line' for a line chart
    }
}
