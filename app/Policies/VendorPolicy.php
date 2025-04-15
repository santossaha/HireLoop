<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view vendors list (filtered by role)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vendor $vendor): bool
    {
        // Admin, founder, and HODs can view any vendor
        if ($user->isAdmin() || $user->isFounder() || $user->isHod()) {
            return true;
        }

        // POC can view vendors they're responsible for
        if ($user->isPoc() && $vendor->internal_poc_id === $user->id) {
            return true;
        }

        // Vendor can view their own profile
        if ($user->isVendor() && $vendor->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, founder, HODs, and POCs can create vendors
        return $user->isAdmin() || $user->isFounder() || $user->isHod() || $user->isPoc();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        // Admin and founder can update any vendor
        if ($user->isAdmin() || $user->isFounder()) {
            return true;
        }

        // HOD can update vendors in their department
        if ($user->isHod()) {
            // Check if the vendor has any requirements in the HOD's department
            // This logic will need to be expanded when requirements are implemented
            return true;
        }

        // POC can update vendors they're responsible for
        if ($user->isPoc() && $vendor->internal_poc_id === $user->id) {
            return true;
        }

        // Vendor can update their own profile (limited fields)
        if ($user->isVendor() && $vendor->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update vendor status.
     */
    public function updateStatus(User $user, Vendor $vendor): bool
    {
        // Only admin, founder, and HODs can approve/reject vendors
        return $user->isAdmin() || $user->isFounder() || $user->isHod();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        // Only admin and founder can delete vendors
        return $user->isAdmin() || $user->isFounder();
    }
}