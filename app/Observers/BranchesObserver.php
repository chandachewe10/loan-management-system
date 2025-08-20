<?php

namespace App\Observers;

use App\Models\Branches;

class BranchesObserver
{
    /**
     * Handle the Branches "created" event.
     */
    public function created(Branches $branches): void
    {

        $branches->organization_id = auth()->user()->organization_id;
        $branches->save();
    }

    /**
     * Handle the Branches "updated" event.
     */
    public function updated(Branches $branches): void
    {
        //
    }

    /**
     * Handle the Branches "deleted" event.
     */
    public function deleted(Branches $branches): void
    {
        //
    }

    /**
     * Handle the Branches "restored" event.
     */
    public function restored(Branches $branches): void
    {
        //
    }

    /**
     * Handle the Branches "force deleted" event.
     */
    public function forceDeleted(Branches $branches): void
    {
        //
    }
}
