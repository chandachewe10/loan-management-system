<?php

namespace App\Filament\Resources\LoanTypeResource\Pages;

use App\Filament\Resources\LoanTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanType extends CreateRecord
{
    protected static string $resource = LoanTypeResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['repayment_amount'] = 10;
     
    //     return $data;
    // }
}
