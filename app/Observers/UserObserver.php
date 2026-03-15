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

        // Assign super_admin role — role may not exist on a fresh NativePHP install
        // so we use firstOrCreate as a safety net
        try {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'super_admin', 'guard_name' => 'web']
            );
            $user->assignRole($role);
        } catch (\Throwable $e) {
            logger()->warning('Could not assign super_admin role to user ' . $user->id . ': ' . $e->getMessage());
        }

        if (is_null($user->organization_id) || empty($user->organization_id)) {
            $user->organization_id = $organization_id;
            $user->save();
        }

        // Create 7 Days free trial
        try {
            Payments::create([
                'organization_id' => $organization_id,
                'payer_id'        => $user->id,
                'payment_amount'  => 0.00,
                'transaction_reference' => random_int(100000, 999999),
                'gateway'         => '7 DAYS FREE TRIAL',
                'payment_made_at' => Carbon::now(),
                'payment_expires_at' => Carbon::now()->addDays(7),
            ]);
        } catch (\Throwable $e) {
            logger()->warning('Could not create free trial for user ' . $user->id . ': ' . $e->getMessage());
        }
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
