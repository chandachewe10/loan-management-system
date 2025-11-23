<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ResetProfileCompletionModal
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Reset profile_completion_modal_shown flag on logout
        // This ensures users are redirected to profile completion on next login if profile is incomplete
        // Use DB facade to avoid session issues during logout
        if ($event->user && $event->user->isProfileIncomplete()) {
            try {
                DB::table('users')
                    ->where('id', $event->user->id)
                    ->update([
                        'profile_completion_modal_shown' => false,
                        'updated_at' => now(),
                    ]);
            } catch (\Exception $e) {
                // Silently fail if there's an issue (e.g., session already destroyed)
                // The flag will be reset on next login attempt anyway
            }
        }
    }
}

