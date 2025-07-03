<?php

namespace App\Policies;

use App\Models\Conference\SubmissionCategoryMajorTrack;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmissionCategoryMajorTrackPolicy
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
    public function view(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
    {
        // dd($submissionCategoryMajorTrack->conference);
        return $user->societies->contains($submissionCategoryMajorTrack->conference->society_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function edit(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
    {
        // dd($submissionCategoryMajorTrack->conference);
        return $user->societies->contains($submissionCategoryMajorTrack->conference->society_id);
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubmissionCategoryMajorTrack $submissionCategoryMajorTrack): bool
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
