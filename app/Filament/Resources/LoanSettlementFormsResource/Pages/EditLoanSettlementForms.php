<?php

namespace App\Filament\Resources\LoanSettlementFormsResource\Pages;

use App\Filament\Resources\LoanSettlementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanSettlementForms extends EditRecord
{
    protected static string $resource = LoanSettlementFormsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
