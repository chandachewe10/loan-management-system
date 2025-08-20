<?php

namespace App\Observers;

use App\Models\Messages;

class MessagesObserver
{
    /**
     * Handle the Messages "created" event.
     */
    public function created(Messages $messages): void
    {

            $messages->organization_id = auth()->user()->organization_id;
            $messages->branch_id = auth()->user()->branch_id;
            $messages->save();

    }

    /**
     * Handle the Messages "updated" event.
     */
    public function updated(Messages $messages): void
    {



    }

    /**
     * Handle the Messages "deleted" event.
     */
    public function deleted(Messages $messages): void
    {
        //
    }

    /**
     * Handle the Messages "restored" event.
     */
    public function restored(Messages $messages): void
    {
        //
    }

    /**
     * Handle the Messages "force deleted" event.
     */
    public function forceDeleted(Messages $messages): void
    {
        //
    }
}
