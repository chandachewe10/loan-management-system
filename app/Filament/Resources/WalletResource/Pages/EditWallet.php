<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Models\Account;
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
        // Update basic wallet fields
        $record->update([
            'name' => $data['name'],
            'amount' => 0,
            'meta' => $data['meta'],
            'description' => strip_tags($data['description'] ?? ''),
        ]);

        // Update Chart of Accounts link
        $oldAccountId = $record->account_id;
        $newAccountId = $data['account_id'] ?? null;

        if ($newAccountId != $oldAccountId) {
            $record->account_id = $newAccountId;
            $record->save();

            // If the old account was an auto-created system account, deactivate it
            if ($oldAccountId) {
                $oldAccount = Account::withoutGlobalScopes()->find($oldAccountId);
                if ($oldAccount && $oldAccount->is_system && str_starts_with($oldAccount->code, '1010-W')) {
                    $oldAccount->update(['is_active' => false]);
                }
            }
        }

        // Keep linked account name in sync
        if ($record->account_id) {
            $linkedAccount = Account::withoutGlobalScopes()->find($record->account_id);
            if ($linkedAccount && $linkedAccount->is_system) {
                $linkedAccount->update([
                    'name' => $data['name'],
                    'description' => 'Linked to wallet: ' . $data['name'],
                ]);
            }
        }

        // Deposit additional funds if provided
        $amount = (float) ($data['amount'] ?? 0);
        if ($amount > 0) {
            $wallet = Wallet::find($record->id);
            $wallet->deposit($amount, ['meta' => $data['description'] ?? 'Deposit']);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
