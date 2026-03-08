<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Models\Account;
use App\Filament\Resources\WalletResource;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wallet;
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

        // Adjust balance if necessary
        $wallet = Wallet::find($record->id);
        $currentBalance = (float) $wallet->balance;
        $newBalance = (float) ($data['balance'] ?? 0);
        $difference = $newBalance - $currentBalance;

        if (abs($difference) >= 0.01) {
            if ($difference > 0) {
                $wallet->deposit($difference, ['meta' => $data['description'] ?? 'Balance adjustment (Increase)']);
            } else {
                $wallet->withdraw(abs($difference), ['meta' => $data['description'] ?? 'Balance adjustment (Decrease)']);
            }
            \App\Services\DoubleEntryService::recordWalletAdjustment($wallet, $difference);
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
