<?php

namespace App\Policies;

use App\Models\Branches;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow viewing branches list if user has view permission or is admin
        return $user->can('branches.view') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Branches $branch): bool
    {
        // Allow viewing specific branch if user has view permission or is admin
        // You can add more specific logic here (e.g., branch-specific access)
        return $user->can('branches.view') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow creating branches only for admins or users with create permission
        return $user->can('branches.create') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Branches $branch): bool
    {
        // Allow updating branches only for admins or users with update permission
        return $user->can('branches.update') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Branches $branch): bool
    {
        // Prevent deletion if branch has users assigned (optional safety check)
        if ($branch->users()->exists()) {
            return false;
        }

        // Allow deletion only for admins or users with delete permission
        return $user->can('branches.delete') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Branches $branch): bool
    {
        // Allow restoring only for admins
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Branches $branch): bool
    {
        // Allow force deletion only for super admins
        return $user->hasRole('super_admin');
    }

    /**
     * Custom method: Determine whether the user can switch to a branch.
     */
    public function switchBranch(User $user, Branches $branch): bool
    {
        // Allow switching if user has access to this branch
        // Example: user's allowed_branches contains this branch ID
        return $user->allowed_branches->contains($branch->id) ||
               $user->hasRole('super_admin');
    }

    /**
     * Custom method: Determine whether the user can manage branch users.
     */
    public function manageUsers(User $user, Branches $branch): bool
    {
        // Allow managing users if user is admin or branch manager
        return $user->hasRole('super_admin') ||
               ($user->can('branches.manage_users') &&
                $user->branch_id === $branch->id);
    }
}
