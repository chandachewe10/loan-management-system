<?php

namespace App\Observers;

use App\Models\Borrower;
Use App\Models\User;
use Illuminate\Support\Facades\Hash;
class BorrowerObserver
{
    /**
     * Handle the Borrower "created" event.
     */
    public function created(Borrower $borrower): void
    {

            $borrower->organization_id = auth()->user()->organization_id;
            $borrower->branch_id = auth()->user()->branch_id;
            $borrower->save();

            // Create the Borrower as a User for Future Authentication
            // User::create([
            //     'name' =>$borrower->first_name. ' '.$borrower->last_name,
            //     'email' => $borrower->email,
            //     'organization_id' => auth()->user()->organization_id,
            //     'password' => Hash::make('test1234'),

            // ]);
    }

    /**
     * Handle the Borrower "updated" event.
     */
    public function updated(Borrower $borrower): void
    {



    }

    /**
     * Handle the Borrower "deleted" event.
     */
    public function deleted(Borrower $borrower): void
    {
        //
    }

    /**
     * Handle the Borrower "restored" event.
     */
    public function restored(Borrower $borrower): void
    {
        //
    }

    /**
     * Handle the Borrower "force deleted" event.
     */
    public function forceDeleted(Borrower $borrower): void
    {
        //
    }
}
