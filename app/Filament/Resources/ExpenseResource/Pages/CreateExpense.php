<?php

namespace App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\ExpenseResource;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $wallet = Wallet::findOrFail($data['from_this_account']);
        $wallet->withdraw($data['expense_amount'], ['meta' => 'Expense amount for ' . $data['expense_name']]);
        $expense = Expense::create([
            'expense_name' => $data['expense_name'],
            'expense_amount' => $data['expense_amount'],
            'expense_vendor' => $data['expense_vendor'],
            'category_id' => $data['category_id'],
            'expense_date' => $data['expense_date'],
            'from_this_account' => $wallet->name,
            'expense_attachment' => $data['expense_attachment'] ?? '',
        ]);
        return $expense;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
