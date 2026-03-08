<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Models\Account;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Create the wallet via Bavix
        $wallet = auth()->user()->createWallet([
            'name' => $data['name'],
            'amount' => 0,
            'organization_id' => auth()->user()->organization_id,
            'branch_id' => auth()->user()->branch_id,
            'slug' => $this->generateSlug($data['name']),
            'meta' => $data['meta'],
            'description' => strip_tags($data['description'] ?? ''),
        ]);

        // 2. Deposit initial funds if provided
        $amount = (float) ($data['balance'] ?? 0);
        if ($amount > 0) {
            $wallet->deposit($amount, ['meta' => 'Initial deposit']);
        }

        // 3. Link to Chart of Accounts
        if (!empty($data['account_id'])) {
            // User manually chose an account
            $wallet->account_id = $data['account_id'];
            $wallet->save();
        } else {
            // Auto-create a sub-account under 1010 Cash & Bank
            $parentAccount = Account::withoutGlobalScopes()->where('code', '1010')->first();
            if ($parentAccount) {
                $subCode = '1010-W' . $wallet->id;
                $account = Account::withoutGlobalScopes()->create([
                    'code' => $subCode,
                    'name' => $wallet->name,
                    'type' => 'asset',
                    'normal_balance' => 'debit',
                    'description' => 'Linked to wallet: ' . $wallet->name,
                    'is_active' => true,
                    'is_system' => true,
                    'parent_id' => $parentAccount->id,
                    'organization_id' => auth()->user()->organization_id,
                    'branch_id' => auth()->user()->branch_id,
                ]);
                $wallet->account_id = $account->id;
                $wallet->save();
            }
        }

        // Record initial balance to Double Entry Service (After CoA Link)
        if ($amount > 0) {
            \App\Services\DoubleEntryService::recordWalletAdjustment($wallet, $amount);
        }

        return $wallet;
    }

    protected function generateSlug($text)
    {
        $slug = str_replace(' ', '-', $text);
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
        $slug = strtolower($slug);
        return $slug;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bank / Cash Account Created')
            ->body('The account has been created and linked to the Chart of Accounts.');
    }
}
