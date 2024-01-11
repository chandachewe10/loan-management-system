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
       
        $records = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $records[] = \App\Models\Loan::whereMonth('created_at', $month)
                ->where('loan_status', '=', 'approved')
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