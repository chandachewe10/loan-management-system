<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {

            $sixRandomFigures = random_int(100000, 999999);
            $userId = $user->id;
            $organization_id = $userId . $sixRandomFigures;
            $user->organization_id = $organization_id;
            $user->save();
            $user->assignRole('super_admin');

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {



    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
