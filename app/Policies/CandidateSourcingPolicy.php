<?php

namespace App\Policies;

use App\Models\CandidateSourcing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CandidateSourcingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPoc() || $user->isBde();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CandidateSourcing $candidateSourcing): bool
    {
        return $user->isPoc() || $user->isBde();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isPoc();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CandidateSourcing $candidateSourcing): bool
    {
        return $user->isBde() && $candidateSourcing->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CandidateSourcing $candidateSourcing): bool
    {
        return false; // No one can delete candidate records
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CandidateSourcing $candidateSourcing): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CandidateSourcing $candidateSourcing): bool
    {
        return false;
    }
}
