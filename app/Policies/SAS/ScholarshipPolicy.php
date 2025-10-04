<?php

namespace App\Policies\SAS;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * ScholarshipPolicy
 *
 * Authorization policy for scholarship-related actions.
 * Implements amount-based approval thresholds per DATA_MODELS.md business rules.
 */
class ScholarshipPolicy
{
    /**
     * Determine if the user can view any scholarships
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewAllScholarships->value)
            || $user->can(Permission::ViewOwnScholarships->value);
    }

    /**
     * Determine if the user can view a specific scholarship
     */
    public function view(User $user, $scholarship): bool
    {
        // System admin can view all
        if ($user->hasRole(Role::SystemAdmin->value)) {
            return true;
        }

        // SAS staff/admin can view all scholarships
        if ($user->can(Permission::ViewAllScholarships->value)) {
            return true;
        }

        // Students can only view their own scholarships
        if ($user->can(Permission::ViewOwnScholarships->value)) {
            return $scholarship->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a scholarship application
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::SubmitScholarshipApplication->value);
    }

    /**
     * Determine if the user can update a scholarship
     */
    public function update(User $user, $scholarship): bool
    {
        // System admin can update all
        if ($user->hasRole(Role::SystemAdmin->value)) {
            return true;
        }

        // SAS staff/admin can update scholarships
        if ($user->hasAnyRole([Role::SasStaff->value, Role::SasAdmin->value])) {
            return true;
        }

        // Students can only update their own pending applications
        if ($user->id === $scholarship->user_id) {
            return in_array($scholarship->status, ['pending_review', 'pending_documents']);
        }

        return false;
    }

    /**
     * Determine if the user can delete a scholarship
     */
    public function delete(User $user, $scholarship): bool
    {
        // Only system admin and SAS admin can delete
        return $user->hasAnyRole([
            Role::SystemAdmin->value,
            Role::SasAdmin->value,
        ]);
    }

    /**
     * Determine if the user can review a scholarship
     */
    public function review(User $user): bool
    {
        return $user->can(Permission::ReviewScholarships->value);
    }

    /**
     * Determine if the user can approve a scholarship
     *
     * Business Rule: Amount-based approval threshold
     * - TES/TDP < ₱20,000: sas_staff can approve
     * - TES/TDP ≥ ₱20,000: requires sas_admin
     *
     * @param  mixed  $scholarship  The scholarship model with amount property
     */
    public function approve(User $user, $scholarship): bool
    {
        // System admin can approve all
        if ($user->hasRole(Role::SystemAdmin->value)) {
            return true;
        }

        // Check if scholarship is in a state that can be approved
        if (! in_array($scholarship->status, ['pending_review', 'under_review'])) {
            return false;
        }

        $amount = $scholarship->amount ?? 0;
        $approvalThreshold = 20000; // ₱20,000

        // For scholarships under ₱20,000
        if ($amount < $approvalThreshold) {
            return $user->can(Permission::ApproveScholarshipsUnder20k->value);
        }

        // For scholarships ≥ ₱20,000 - requires admin
        return $user->can(Permission::ApproveScholarshipsOver20k->value);
    }

    /**
     * Determine if the user can reject a scholarship
     */
    public function reject(User $user): bool
    {
        return $user->can(Permission::RejectScholarships->value);
    }

    /**
     * Determine if the user can disburse a scholarship
     */
    public function disburse(User $user, $scholarship): bool
    {
        // Only approved scholarships can be disbursed
        if ($scholarship->status !== 'approved') {
            return false;
        }

        return $user->can(Permission::DisbursScholarships->value);
    }

    /**
     * Determine if user can view scholarship reports
     */
    public function viewReports(User $user): bool
    {
        return $user->can(Permission::ViewSasReports->value);
    }
}
