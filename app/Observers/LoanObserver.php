<?php

namespace App\Observers;

use App\Models\Loan;
use App\Services\DoubleEntryService;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        $loan->organization_id = auth()->user()->organization_id;
        $loan->branch_id = auth()->user()->branch_id;
        $loan->save();
    }

    /**
     * Handle the Loan "updated" event.
     * When loan_status transitions to 'active' (approved/disbursed), post the disbursement journal entry.
     */
    public function updated(Loan $loan): void
    {
        // Trigger double-entry when loan becomes active/disbursed
        $statusChanged = $loan->wasChanged('loan_status');
        $nowActive = in_array($loan->loan_status, ['active', 'approved', 'disbursed']);
        $wasNotActive = !in_array($loan->getOriginal('loan_status'), ['active', 'approved', 'disbursed']);

        if ($statusChanged && $nowActive && $wasNotActive) {
            DoubleEntryService::recordLoanDisbursement($loan);
        }
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "restored" event.
     */
    public function restored(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        //
    }
}
