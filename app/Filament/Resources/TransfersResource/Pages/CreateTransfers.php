<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\TransfersResource;
use Filament\Notifications\Notification;
use Bavix\Wallet\Models\Wallet;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


class CreateTransfers extends CreateRecord
{
    protected static string $resource = TransfersResource::class;
    protected static ?string $navigationIcon = 'fas-wallet';
    protected static ?string $navigationGroup = 'Wallets';

    protected ?string $heading = 'Funds Transfer';

    protected function handleRecordCreation(array $data): Model
    {



        $firstWallet = Wallet::findOrFail($data['from_this_account']);
        $lastWallet = Wallet::findOrFail($data['to_this_account']);
        $firstWalletCurrency = $firstWallet->meta['currency'];
        $lastWalletCurrency = $lastWallet->meta['currency'];

         if($firstWalletCurrency != $lastWalletCurrency){
          Notification::make()
            ->warning()
            ->title('Cross Currency Transfer')
            ->body('The system does not currently support cross currency transfer from one currency to the other. You are trying to transfer
             from '.strtoupper($firstWalletCurrency) .' to '. strtoupper($lastWalletCurrency))
            ->persistent()
            ->send();
            $this->halt();
}


 if($firstWallet->name === $lastWallet->name){
          Notification::make()
            ->warning()
            ->title('Same Bank Virtual Account')
            ->body('You cant transfer to the same wallet. Please choose a different wallet')
            ->persistent()
            ->send();
            $this->halt();
}




        try{



            $firstWallet->transfer($lastWallet, $data['amount_to_transfer'], [
           'organization_id' => auth()->user()->organization_id,
           'branch_id' => auth()->user()->branch_id,
]);




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



        return $firstWallet;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transfer')
            ->body('The Transfer has been initiated successfully.');
    }
}
