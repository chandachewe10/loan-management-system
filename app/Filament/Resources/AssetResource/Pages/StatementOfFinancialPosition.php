<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Resources\Pages\Page;
use App\Services\StatementOfFinancialPosition as Service; 

class StatementOfFinancialPosition extends Page
{
    protected static string $resource = AssetResource::class;
    protected static string $view = 'filament.resources.asset-resource.pages.statement-of-financial-position';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Statement of Financial Position';

    // Define public properties for the totals
    public $cashAmount = 0;
    public $loansAmount = 0;
    public $equipmentAmount = 0;
    public $totalAssets = 0;
    public $totalLiabilities = 0;
    public $totalEquity = 0;

    public function mount(): void
    {
        $statement = new Service();

        $this->cashAmount = $statement->cashAmount();
        $this->equipmentAmount = $statement->equipmentAmount();

        $this->totalAssets = $statement->totalAssets();
        $this->totalLiabilities = $statement->totalLiabilities();
        $this->totalEquity = $statement->totalEquity();
    }
}
