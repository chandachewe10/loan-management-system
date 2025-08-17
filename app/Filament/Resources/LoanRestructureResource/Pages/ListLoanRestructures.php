<?php

namespace App\Filament\Resources\LoanRestructureResource\Pages;

use App\Filament\Resources\LoanRestructureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanRestructures extends ListRecords
{
    protected static string $resource = LoanRestructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
