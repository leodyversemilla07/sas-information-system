<?php

namespace App\Policies\USG;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * ResolutionPolicy
 *
 * Authorization policy for USG resolution management
 */
class ResolutionPolicy
{
    /**
     * Determine if the user can view any resolutions
     */
    public function viewAny(User $user): bool
    {
        return true; // Resolutions are publicly viewable
    }

    /**
     * Determine if the user can view a specific resolution
     */
    public function view(User $user, $resolution): bool
    {
        return true; // Resolutions are publicly viewable
    }

    /**
     * Determine if the user can create a resolution
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CreateResolutions->value);
    }

    /**
     * Determine if the user can update a resolution
     */
    public function update(User $user, $resolution): bool
    {
        // System admin and USG roles can update
        if ($user->hasAnyRole([
            Role::SystemAdmin->value,
            Role::UsgOfficer->value,
            Role::UsgAdmin->value,
        ])) {
            return true;
        }

        // Creator can update their own unpublished resolutions
        if ($resolution->created_by === $user->id && $resolution->status !== 'published') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete a resolution
     */
    public function delete(User $user, $resolution): bool
    {
        return $user->can(Permission::DeleteResolutions->value);
    }

    /**
     * Determine if the user can publish a resolution
     */
    public function publish(User $user, $resolution): bool
    {
        return $user->can(Permission::PublishResolutions->value);
    }

    /**
     * Determine if the user can manage resolutions
     */
    public function manage(User $user): bool
    {
        return $user->can(Permission::ManageResolutions->value);
    }
}
