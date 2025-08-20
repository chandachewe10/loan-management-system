<?php

namespace App\Observers;

use App\Models\LoanAgreementForms;

class LoanAgreementFormsObserver
{
    /**
     * Handle the LoanAgreementForms "created" event.
     */
    public function created(LoanAgreementForms $loanAgreementForms): void
    {

            $loanAgreementForms->organization_id = auth()->user()->organization_id;
            $loanAgreementForms->branch_id = auth()->user()->branch_id;
            $loanAgreementForms->save();
    }

    /**
     * Handle the LoanAgreementForms "updated" event.
     */
    public function updated(LoanAgreementForms $loanAgreementForms): void
    {



    }

    /**
     * Handle the LoanAgreementForms "deleted" event.
     */
    public function deleted(LoanAgreementForms $loanAgreementForms): void
    {
        //
    }

    /**
     * Handle the LoanAgreementForms "restored" event.
     */
    public function restored(LoanAgreementForms $loanAgreementForms): void
    {
        //
    }

    /**
     * Handle the LoanAgreementForms "force deleted" event.
     */
    public function forceDeleted(LoanAgreementForms $loanAgreementForms): void
    {
        //
    }
}
