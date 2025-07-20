<?php

namespace App\Filament\Resources\LoanAgreementFormsResource\Pages;

use App\Filament\Resources\LoanAgreementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLoanAgreementForms extends CreateRecord
{
    protected static string $resource = LoanAgreementFormsResource::class;

       protected function getRedirectUrl(): string
    {
       
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Loan Agreement Form')
            ->body('The Loan agreement form has been created successfully.');
    }
}
