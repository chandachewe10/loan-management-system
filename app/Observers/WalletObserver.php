<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Wallet;

class WalletObserver
{
    /**
     * Handle the Wallet "created" event.
     * Only sets org/branch. Account linking is handled by CreateWallet page.
     */
    public function created(Wallet $wallet): void
    {
        // org/branch are set by CreateWallet page, but ensure they're present
        if (auth()->check() && !$wallet->organization_id) {
            $wallet->organization_id = auth()->user()->organization_id;
            $wallet->branch_id = auth()->user()->branch_id;
            $wallet->saveQuietly();
        }
    }

    /**
     * Handle the Wallet "updated" event.
     * Keep the linked account name in sync.
     */
    public function updated(Wallet $wallet): void
    {
        if ($wallet->wasChanged('name') && $wallet->account_id) {
            $account = Account::withoutGlobalScopes()->find($wallet->account_id);
            if ($account && $account->is_system) {
                $account->update([
                    'name' => $wallet->name,
                    'description' => 'Linked to wallet: ' . $wallet->name,
                ]);
            }
        }
    }

    public function deleted(Wallet $wallet): void
    {
        if ($wallet->account_id) {
            Account::withoutGlobalScopes()
                ->where('id', $wallet->account_id)
                ->update(['is_active' => false]);
        }
    }

    public function restored(Wallet $wallet): void
    {
    }
    public function forceDeleted(Wallet $wallet): void
    {
    }
}
