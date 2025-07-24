<?php

namespace App\Observers;

use App\Models\BorrowerFiles;

class BorrowerFilesObserver
{
    /**
     * Handle the BorrowerFiles "created" event.
     */
    public function created(BorrowerFiles $borrowerFiles): void
    {
       
            $borrowerFiles->organization_id = auth()->user()->organization_id;
        
    }

    /**
     * Handle the BorrowerFiles "updated" event.
     */
    public function updated(BorrowerFiles $borrowerFiles): void
    {
       
           
        
    }

    /**
     * Handle the BorrowerFiles "deleted" event.
     */
    public function deleted(BorrowerFiles $borrowerFiles): void
    {
        //
    }

    /**
     * Handle the BorrowerFiles "restored" event.
     */
    public function restored(BorrowerFiles $borrowerFiles): void
    {
        //
    }

    /**
     * Handle the BorrowerFiles "force deleted" event.
     */
    public function forceDeleted(BorrowerFiles $borrowerFiles): void
    {
        //
    }
}
