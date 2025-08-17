<?php

namespace App\Filament\Resources\LoanRollOverResource\Pages;

use App\Filament\Resources\LoanRollOverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanRollOvers extends ListRecords
{
    protected static string $resource = LoanRollOverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
