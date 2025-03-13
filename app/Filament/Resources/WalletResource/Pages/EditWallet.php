<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use Illuminate\Database\Eloquent\Model;
use Bavix\Wallet\Models\Wallet;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWallet extends EditRecord
{
    protected static string $resource = WalletResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'name' => $data['name'],
            'amount' => 0,
            // 'meta' => ['currency' =>  $data['meta']],
            'description' => strip_tags($data['description']),
        ]);
        $wallet = Wallet::where('name', '=', $data['name'])->first();

        $wallet->deposit($data['amount'], ['meta' => $data['description']]);
        return $record;
    }
    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
