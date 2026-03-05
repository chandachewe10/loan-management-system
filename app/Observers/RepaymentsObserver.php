<?php

namespace App\Observers;

use App\Models\Repayments;
use App\Services\DoubleEntryService;

class RepaymentsObserver
{
    /**
     * Handle the Repayments "created" event.
     * Automatically post a double-entry journal entry for the repayment.
     */
    public function created(Repayments $repayments): void
    {
        $repayments->organization_id = auth()->user()->organization_id;
        $repayments->branch_id = auth()->user()->branch_id;
        $repayments->save();

        // Post journal entry for the repayment
        DoubleEntryService::recordLoanRepayment($repayments);
    }

    /**
     * Handle the Repayments "updated" event.
     */
    public function updated(Repayments $repayments): void
    {
        //
    }

    /**
     * Handle the Repayments "deleted" event.
     */
    public function deleted(Repayments $repayments): void
    {
        //
    }

    /**
     * Handle the Repayments "restored" event.
     */
    public function restored(Repayments $repayments): void
    {
        //
    }

    /**
     * Handle the Repayments "force deleted" event.
     */
    public function forceDeleted(Repayments $repayments): void
    {
        //
    }
}
