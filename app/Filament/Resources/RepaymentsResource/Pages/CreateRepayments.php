<?php

namespace App\Filament\Resources\RepaymentsResource\Pages;
use Illuminate\Database\Eloquent\Model;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RepaymentsResource;
use App\Models\Repayments;
use App\Models\Loan;

class CreateRepayments extends CreateRecord
{
    protected static string $resource = RepaymentsResource::class;
    protected function handleRecordCreation(array $data): Model
    {
       
        $loan = Loan::findOrFail($data['loan_id']);
        $wallet = Wallet::where('name',"=",$loan->from_this_account)->first();  
        $principal_amount = $loan->principal_amount;
        $loan_number = $loan->loan_number;
        $old_balance = (float) ($loan->balance);
        $new_balance = ($old_balance) - ((float) ($data['payments']));
        
        $repayment = Repayments::create([
            'loan_id' => $data['loan_id'],
            'payments' => $data['payments'],
            'balance' => $new_balance,
            'payments_method' => $data['payments_method'],
            'reference_number' => $data['reference_number'],
            'loan_number' => $loan_number,
            'principal' => $principal_amount,
           
        ]);
        //update Balance in Loans Table
        $loan->update([
            'balance' => $new_balance
        ]);
       
        $wallet->deposit($data['payments'], ['meta' => 'Loan repayment amount']);
       
        return $repayment;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
