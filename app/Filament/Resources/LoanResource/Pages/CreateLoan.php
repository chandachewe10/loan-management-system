<?php

namespace App\Filament\Resources\LoanResource\Pages;

use Carbon\Carbon;
use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $loan_cycle = \App\Models\LoanType::findOrFail($data['loan_type_id'])->interest_cycle;
        $loan_duration = $data['loan_duration'];
        $loan_release_date = $data['loan_release_date'];
        $loan_date = Carbon::createFromFormat('Y-m-d', $loan_release_date);

        if ($loan_cycle === 'daily') {
            $data['loan_due_date'] = $loan_date->addDays($loan_duration);
            return $data;
        }
        if ($loan_cycle === 'weekly') {
            $data['loan_due_date'] = $loan_date->addWeeks($loan_duration);
            return $data;
        }
        if ($loan_cycle === 'monthly') {
            $data['loan_due_date'] = $loan_date->addMonths($loan_duration);
            return $data;
        }
        if ($loan_cycle === 'yearly') {
            $data['loan_due_date'] = $loan_date->addYears($loan_duration);
            return $data;
        }
    }
}
