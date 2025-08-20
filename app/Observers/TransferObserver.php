<?php

namespace App\Observers;

use App\Models\Transfer;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
         $transfer->organization_id = auth()->user()->organization_id;
         $transfer->branch_id = auth()->user()->branch_id;
         $transfer->save();
    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Transfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        //
    }

    /**
     * Handle the Transfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        //
    }
}
