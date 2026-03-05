<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $account): void
    {
        if (auth()->check() && !$account->organization_id) {
            $account->organization_id = auth()->user()->organization_id;
            $account->branch_id = auth()->user()->branch_id;
            $account->save();
        }
    }

    public function updated(Account $account): void
    {
    }
    public function deleted(Account $account): void
    {
    }
    public function restored(Account $account): void
    {
    }
    public function forceDeleted(Account $account): void
    {
    }
}
