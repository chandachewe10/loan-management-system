<?php

namespace App\Filament\Resources\LoanSettlementFormsResource\Pages;

use App\Filament\Resources\LoanSettlementFormsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLoanSettlementForms extends CreateRecord
{
    protected static string $resource = LoanSettlementFormsResource::class;


       protected function getRedirectUrl(): string
    {
       
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Loan settlement Form')
            ->body('The Loan settlement form has been created successfully.');
    }
}
