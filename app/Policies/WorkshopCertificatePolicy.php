<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop\WorkshopCertificate;
use Illuminate\Auth\Access\Response;

class WorkshopCertificatePolicy
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
    public function view(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return $user->societies->contains($workshopCertificate->workshop->conference->society_id);
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
    public function update(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return false;
    }
    
    public function edit(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return $user->societies->contains($workshopCertificate->workshop->conference->society_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkshopCertificate $workshopCertificate): bool
    {
        return false;
    }
}
