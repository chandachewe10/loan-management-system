<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\TransfersResource;
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
        $firstWallet->transfer($lastWallet, $data['amount_to_transfer']);

        return $firstWallet;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
