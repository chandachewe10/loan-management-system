<?php

namespace App\Filament\Resources\LoanTypeResource\Pages;

use App\Filament\Resources\LoanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLoanType extends CreateRecord
{
    protected static string $resource = LoanTypeResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['repayment_amount'] = 10;
     
    //     return $data;
    // }


       protected function getRedirectUrl(): string
    {
       
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Loan type')
            ->body('The loan type has been created successfully.');
    }
}
