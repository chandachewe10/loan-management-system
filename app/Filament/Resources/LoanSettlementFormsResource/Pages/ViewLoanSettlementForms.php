<?php

namespace App\Filament\Resources\LoanSettlementFormsResource\Pages;

use App\Filament\Resources\LoanSettlementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanSettlementForms extends ViewRecord
{
    protected static string $resource = LoanSettlementFormsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
