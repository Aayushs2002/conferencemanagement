<?php

namespace App\Policies;

use App\Models\Sponsor\Sponsor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SponsorPolicy
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
    public function view(User $user, Sponsor $sponsor): bool
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

    public function edit(User $user, Sponsor $sponsor): bool
    {
        // dd($sponsor->conference);  
        return $user->societies->contains($sponsor->conference->society_id);
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sponsor $sponsor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sponsor $sponsor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sponsor $sponsor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sponsor $sponsor): bool
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
