<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('view_role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Prevent creating new super_admin roles
        return $user->can('create_role') && !$this->isSuperAdminRole($roleName ?? null);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Prevent updating super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return false;
        }

        return $user->can('update_role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Prevent deleting super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return false;
        }

        return $user->can('delete_role');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_role');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        // Prevent force deleting super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return false;
        }

        return $user->can('force_delete_role');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_role');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Role $role): bool
    {
        // Prevent restoring to super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return false;
        }

        return $user->can('restore_role');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_role');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Role $role): bool
    {
        // Prevent replicating super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return false;
        }

        return $user->can('replicate_role');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_roles');
    }

    /**
     * Check if the role is a super_admin role
     */
    protected function isSuperAdminRole(?string $roleName): bool
    {
        // Add all variations of super admin role names here
        $protectedRoles = ['super_admin', 'super-admin', 'super admin', 'superadministrator'];

        return in_array(strtolower($roleName), array_map('strtolower', $protectedRoles));
    }

    /**
     * Custom method to check if user can assign this role
     */
    public function assign(User $user, Role $role): bool
    {
        // Prevent assigning super_admin role
        if ($this->isSuperAdminRole($role->name)) {
            return $user->hasRole('super_admin'); // Only super admins can assign super_admin
        }

        return $user->can('assign_roles');
    }
}
