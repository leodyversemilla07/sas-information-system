<?php

namespace App\Policies\SAS;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * EventPolicy
 *
 * Authorization policy for event management
 */
class EventPolicy
{
    /**
     * Determine if the user can view any events
     */
    public function viewAny(User $user): bool
    {
        return true; // Events are publicly viewable
    }

    /**
     * Determine if the user can view a specific event
     */
    public function view(User $user, $event): bool
    {
        return true; // Events are publicly viewable
    }

    /**
     * Determine if the user can create an event
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CreateEvents->value);
    }

    /**
     * Determine if the user can update an event
     */
    public function update(User $user, $event): bool
    {
        // System admin and SAS roles can update
        if ($user->hasAnyRole([
            Role::SystemAdmin->value,
            Role::SasStaff->value,
            Role::SasAdmin->value,
        ])) {
            return true;
        }

        // Event creator can update their own unpublished events
        if ($event->created_by === $user->id && $event->status !== 'published') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete an event
     */
    public function delete(User $user, $event): bool
    {
        return $user->can(Permission::DeleteEvents->value);
    }

    /**
     * Determine if the user can publish an event
     */
    public function publish(User $user, $event): bool
    {
        return $user->can(Permission::PublishEvents->value);
    }

    /**
     * Determine if the user can manage events
     */
    public function manage(User $user): bool
    {
        return $user->can(Permission::ManageEvents->value);
    }
}
