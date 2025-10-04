<?php

namespace App\Policies\USG;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * AnnouncementPolicy
 *
 * Authorization policy for USG announcement management
 */
class AnnouncementPolicy
{
    /**
     * Determine if the user can view any announcements
     */
    public function viewAny(User $user): bool
    {
        return true; // Announcements are publicly viewable
    }

    /**
     * Determine if the user can view a specific announcement
     */
    public function view(User $user, $announcement): bool
    {
        return true; // Announcements are publicly viewable
    }

    /**
     * Determine if the user can create an announcement
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CreateAnnouncements->value);
    }

    /**
     * Determine if the user can update an announcement
     */
    public function update(User $user, $announcement): bool
    {
        // System admin and USG roles can update
        if ($user->hasAnyRole([
            Role::SystemAdmin->value,
            Role::UsgOfficer->value,
            Role::UsgAdmin->value,
        ])) {
            return true;
        }

        // Creator can update their own unpublished announcements
        if ($announcement->created_by === $user->id && ! $announcement->published_at) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete an announcement
     */
    public function delete(User $user, $announcement): bool
    {
        return $user->can(Permission::DeleteAnnouncements->value);
    }

    /**
     * Determine if the user can publish an announcement
     */
    public function publish(User $user, $announcement): bool
    {
        return $user->can(Permission::PublishAnnouncements->value);
    }

    /**
     * Determine if the user can manage announcements
     */
    public function manage(User $user): bool
    {
        return $user->can(Permission::ManageAnnouncements->value);
    }
}
