<?php

namespace App\Observers;

use App\Models\Borrower;

class BorrowerObserver
{
    /**
     * Handle the Borrower "created" event.
     */
    public function created(Borrower $borrower): void
    {
        if (auth()->hasUser()) {
            $borrower->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the Borrower "updated" event.
     */
    public function updated(Borrower $borrower): void
    {
        if (auth()->hasUser()) {
            $borrower->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the Borrower "deleted" event.
     */
    public function deleted(Borrower $borrower): void
    {
        //
    }

    /**
     * Handle the Borrower "restored" event.
     */
    public function restored(Borrower $borrower): void
    {
        //
    }

    /**
     * Handle the Borrower "force deleted" event.
     */
    public function forceDeleted(Borrower $borrower): void
    {
        //
    }
}
