<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResetProfileCompletionModal
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Reset profile_completion_modal_shown flag on logout
        // This ensures users are redirected to profile completion on next login if profile is incomplete
        if ($event->user && $event->user->isProfileIncomplete()) {
            $event->user->update([
                'profile_completion_modal_shown' => false,
            ]);
        }
    }
}

