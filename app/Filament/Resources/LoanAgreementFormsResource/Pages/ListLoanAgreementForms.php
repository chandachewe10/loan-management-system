<?php

namespace App\Filament\Resources\LoanAgreementFormsResource\Pages;

use App\Filament\Resources\LoanAgreementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanAgreementForms extends ListRecords
{
    protected static string $resource = LoanAgreementFormsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
