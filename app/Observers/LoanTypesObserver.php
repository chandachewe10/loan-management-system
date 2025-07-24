<?php

namespace App\Observers;

use App\Models\LoanType;

class LoanTypesObserver
{
    /**
     * Handle the LoanTypes "created" event.
     */
    public function created(LoanType $loanTypes): void
    {
        if (auth()->hasUser()) {
            $loanTypes->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the LoanTypes "updated" event.
     */
    public function updated(LoanType $loanTypes): void
    {
        if (auth()->hasUser()) {
            $loanTypes->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the LoanTypes "deleted" event.
     */
    public function deleted(LoanType $loanTypes): void
    {
        //
    }

    /**
     * Handle the LoanTypes "restored" event.
     */
    public function restored(LoanType $loanTypes): void
    {
        //
    }

    /**
     * Handle the LoanTypes "force deleted" event.
     */
    public function forceDeleted(LoanType $loanTypes): void
    {
        //
    }
}
