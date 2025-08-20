<?php

namespace App\Observers;

use App\Models\Wallet;

class WalletObserver
{
    /**
     * Handle the BavixWalletModelsWallet "created" event.
     */
    public function created(Wallet $bavixWalletModelsWallet): void
    {
         $bavixWalletModelsWallet->organization_id = auth()->user()->organization_id;
         $bavixWalletModelsWallet->branch_id = auth()->user()->branch_id;
         $bavixWalletModelsWallet->save();
    }

    /**
     * Handle the BavixWalletModelsWallet "updated" event.
     */
    public function updated(Wallet $bavixWalletModelsWallet): void
    {
        //
    }

    /**
     * Handle the BavixWalletModelsWallet "deleted" event.
     */
    public function deleted(Wallet $bavixWalletModelsWallet): void
    {
        //
    }

    /**
     * Handle the BavixWalletModelsWallet "restored" event.
     */
    public function restored(Wallet $bavixWalletModelsWallet): void
    {
        //
    }

    /**
     * Handle the BavixWalletModelsWallet "force deleted" event.
     */
    public function forceDeleted(Wallet $bavixWalletModelsWallet): void
    {
        //
    }
}
