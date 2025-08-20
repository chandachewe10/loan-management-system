<?php

namespace App\Observers;

use App\Models\ThirdParty;

class ThirdyPartyObserver
{
    /**
     * Handle the ThirdParty "created" event.
     */
    public function created(ThirdParty $thirdParty): void
    {

            $thirdParty->organization_id = auth()->user()->organization_id;
            $thirdParty->branch_id = auth()->user()->branch_id;
            $thirdParty->save();
    }

    /**
     * Handle the ThirdParty "updated" event.
     */
    public function updated(ThirdParty $thirdParty): void
    {



    }

    /**
     * Handle the ThirdParty "deleted" event.
     */
    public function deleted(ThirdParty $thirdParty): void
    {
        //
    }

    /**
     * Handle the ThirdParty "restored" event.
     */
    public function restored(ThirdParty $thirdParty): void
    {
        //
    }

    /**
     * Handle the ThirdParty "force deleted" event.
     */
    public function forceDeleted(ThirdParty $thirdParty): void
    {
        //
    }
}
