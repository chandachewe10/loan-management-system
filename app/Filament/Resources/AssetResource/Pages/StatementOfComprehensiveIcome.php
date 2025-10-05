<?php

namespace App\Filament\Resources\AssetResource\Pages;

use Filament\Resources\Pages\Page;
use App\Services\StatementOfComprehensiveIncome as Service;
use App\Filament\Resources\AssetResource;

class StatementOfComprehensiveIncome extends Page
{
    protected static string $resource = AssetResource::class;
    protected static string $view = 'filament.resources.asset-resource.pages.statement-of-comprehensive-income';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Statement of Comprehensive Income';

    public $interestIncome = 0;
    public $serviceFeeIncome = 0;
    public $totalIncome = 0;
    public $totalExpenses = 0;
    public $badLoans = 0;
    public $netProfit = 0;

    public function mount(): void
    {
        $service = new Service();
        $report = $service->getReportData();

        $this->interestIncome = $report['interestIncome'];
        $this->serviceFeeIncome = $report['serviceFeeIncome'];
        $this->totalIncome = $report['totalIncome'];
        $this->badLoans = $report['badLoans'];
        $this->totalExpenses = $report['totalExpenses'];
        $this->netProfit = $report['netProfit'];
    }
}
