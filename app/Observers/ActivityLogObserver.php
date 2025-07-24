<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityLogObserver
{
    /**
     * Handle the ActivityLog "created" event.
     */
    public function created(ActivityLog $activityLog): void
    {
        if (auth()->hasUser()) {
            $activityLog->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the ActivityLog "updated" event.
     */
    public function updated(ActivityLog $activityLog): void
    {
        if (auth()->hasUser()) {
            $activityLog->organization_id = auth()->user()->organization_id;
        }
    }

    /**
     * Handle the ActivityLog "deleted" event.
     */
    public function deleted(ActivityLog $activityLog): void
    {
        //
    }

    /**
     * Handle the ActivityLog "restored" event.
     */
    public function restored(ActivityLog $activityLog): void
    {
        //
    }

    /**
     * Handle the ActivityLog "force deleted" event.
     */
    public function forceDeleted(ActivityLog $activityLog): void
    {
        //
    }
}
