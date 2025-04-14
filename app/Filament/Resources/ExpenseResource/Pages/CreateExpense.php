<?php

namespace App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\ExpenseResource;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{

   
       
        $wallet = Wallet::findOrFail($data['from_this_account']);

        try{
            $wallet->withdraw($data['expense_amount'], ['meta' => 'Expense amount for ' . $data['expense_name']]);
        }
        catch(\Exception $e){
            Notification::make()
            ->warning()
            ->title('Problem With Wallet')
            ->body('Whoops, something went wrong: ' . $e->getMessage())
            ->persistent()
          
            ->send();
        
        $this->halt();
        }

        
        $data['from_this_account'] = $wallet->name;
       
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
