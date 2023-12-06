<?php

namespace App\Filament\Resources\LoanAgreementFormsResource\Pages;

use App\Filament\Resources\LoanAgreementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanAgreementForms extends ViewRecord
{
    protected static string $resource = LoanAgreementFormsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
