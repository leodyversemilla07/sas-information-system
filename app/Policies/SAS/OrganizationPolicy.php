<?php

namespace App\Policies\SAS;

use App\Enums\Permission;
use App\Models\User;

/**
 * OrganizationPolicy
 *
 * Authorization policy for student organization management
 */
class OrganizationPolicy
{
    /**
     * Determine if the user can view any organizations
     */
    public function viewAny(User $user): bool
    {
        return true; // Organizations are publicly viewable
    }

    /**
     * Determine if the user can view a specific organization
     */
    public function view(User $user, $organization): bool
    {
        return true; // Organizations are publicly viewable
    }

    /**
     * Determine if the user can create an organization
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CreateOrganizations->value);
    }

    /**
     * Determine if the user can update an organization
     */
    public function update(User $user, $organization): bool
    {
        return $user->can(Permission::UpdateOrganizations->value)
            || $user->can(Permission::ManageOrganizations->value);
    }

    /**
     * Determine if the user can delete an organization
     */
    public function delete(User $user, $organization): bool
    {
        return $user->can(Permission::DeleteOrganizations->value);
    }

    /**
     * Determine if the user can approve an organization
     */
    public function approve(User $user, $organization): bool
    {
        return $user->can(Permission::ApproveOrganizations->value);
    }

    /**
     * Determine if the user can manage organization members
     */
    public function manageMembers(User $user, $organization): bool
    {
        return $user->can(Permission::ManageOrganizations->value);
    }
}
