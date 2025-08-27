<?php

namespace App\Policies;

use App\Models\Conference\ScientificSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScientificSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScientificSession $scientificSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScientificSession $scientificSession): bool
    {
        return false;
    }

    public function edit(User $user, ScientificSession $scientificSession): bool
    {
        return $user->societies->contains($scientificSession->conference->society_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScientificSession $scientificSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ScientificSession $scientificSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ScientificSession $scientificSession): bool
    {
        return false;
    }

    public function before(User $user, string $ability)
    {
        if ($user->type == 1) {
            return true;
        }
    }
}
