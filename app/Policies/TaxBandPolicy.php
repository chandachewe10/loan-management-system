<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaxBand;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxBandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tax::band');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxBand $taxBand): bool
    {
        return $user->can('view_tax::band');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tax::band');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxBand $taxBand): bool
    {
        return $user->can('update_tax::band');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxBand $taxBand): bool
    {
        return $user->can('delete_tax::band');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tax::band');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TaxBand $taxBand): bool
    {
        return $user->can('force_delete_tax::band');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tax::band');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TaxBand $taxBand): bool
    {
        return $user->can('restore_tax::band');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tax::band');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TaxBand $taxBand): bool
    {
        return $user->can('replicate_tax::band');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_tax::band');
    }
}
