<?php
 
namespace App\Filament\Widgets;
 
use Filament\Widgets\LineChartWidget;
 
class LineCharts extends LineChartWidget
{
    protected static ?string $maxHeight = '200px';
    protected static ?int $sort = 1;
    public function getHeading(): string
    {
        return 'Funds Disbursed';
    }
 
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total principle released',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];

        
    }
    
}