<?php

namespace App\Filament\Resources\LoanAgreementFormsResource\Pages;

use App\Filament\Resources\LoanAgreementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanAgreementForms extends EditRecord
{
    protected static string $resource = LoanAgreementFormsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
