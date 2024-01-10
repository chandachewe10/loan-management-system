<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PieChart extends ChartWidget
{
    protected static ?string $heading = 'Blog Posts';
    protected static ?string $maxHeight = '200px';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
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
