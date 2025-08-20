<?php

namespace App\Observers;

use App\Models\LoanSettlementForms;

class LoanSettlementFormsObserver
{
    /**
     * Handle the LoanSettlementForms "created" event.
     */
    public function created(LoanSettlementForms $loanSettlementForms): void
    {

            $loanSettlementForms->organization_id = auth()->user()->organization_id;
            $loanSettlementForms->branch_id = auth()->user()->branch_id;
            $loanSettlementForms->save();
    }

    /**
     * Handle the LoanSettlementForms "updated" event.
     */
    public function updated(LoanSettlementForms $loanSettlementForms): void
    {



    }

    /**
     * Handle the LoanSettlementForms "deleted" event.
     */
    public function deleted(LoanSettlementForms $loanSettlementForms): void
    {
        //
    }

    /**
     * Handle the LoanSettlementForms "restored" event.
     */
    public function restored(LoanSettlementForms $loanSettlementForms): void
    {
        //
    }

    /**
     * Handle the LoanSettlementForms "force deleted" event.
     */
    public function forceDeleted(LoanSettlementForms $loanSettlementForms): void
    {
        //
    }
}
