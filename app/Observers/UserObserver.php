<?php

namespace App\Observers;

use App\Models\{User,Payments};
use Carbon\Carbon;

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

            $user->assignRole('super_admin');

            if(is_null($user->organization_id) || empty($user->organization_id)){
            $user->organization_id = $organization_id;
            $user->save();
            }

            // Create 7 Days free trial
             Payments::create([
            'organization_id' => $organization_id,
            'payer_id' => $user->id,
            'payment_amount' => 0.00,
            'transaction_reference' => random_int(100000, 999999),
            'gateway' => '7 DAYS FREE TRIAL',
            'payment_made_at' => Carbon::now(),
            'payment_expires_at' => Carbon::now()->addDays(7),
        ]);


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
