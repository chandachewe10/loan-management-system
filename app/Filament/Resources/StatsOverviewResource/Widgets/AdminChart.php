<?php

namespace App\Filament\Resources\StatsOverviewResource\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;

class AdminChart extends ChartWidget
{
    public static function getWidgets(): array
{
    return [
        AdminChart::class,
    ];
}
public static function getHeaderWidgets(): array
{
    return [
        AdminChart::class,
    ];
}
    protected static ?string $heading = 'Total Disbursed';
    public static string $resource = Loan::class;
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
