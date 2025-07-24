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
        if (auth()->hasUser()) {
            $thirdParty->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the ThirdParty "updated" event.
     */
    public function updated(ThirdParty $thirdParty): void
    {
        if (auth()->hasUser()) {
            $thirdParty->organization_id = auth()->user()->organization_id;
        }
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
