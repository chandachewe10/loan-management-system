<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityLogs;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_activitylog');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('view_activitylog');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_activitylog');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('update_activitylog');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('delete_activitylog');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_activitylog');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('force_delete_activitylog');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_activitylog');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('restore_activitylog');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_activitylog');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ActivityLogs $activityLogs): bool
    {
        return $user->can('replicate_activitylog');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_activitylog');
    }
}
