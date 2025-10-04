<?php

namespace App\Policies\Registrar;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

/**
 * DocumentRequestPolicy
 *
 * Authorization policy for document request management
 */
class DocumentRequestPolicy
{
    /**
     * Determine if the user can view any document requests
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewAllDocumentRequests->value)
            || $user->can(Permission::ViewOwnDocumentRequests->value);
    }

    /**
     * Determine if the user can view a specific document request
     */
    public function view(User $user, $documentRequest): bool
    {
        // System admin and registrar staff can view all
        if ($user->can(Permission::ViewAllDocumentRequests->value)) {
            return true;
        }

        // Students can only view their own requests
        if ($user->can(Permission::ViewOwnDocumentRequests->value)) {
            return $documentRequest->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a document request
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::RequestDocuments->value);
    }

    /**
     * Determine if the user can update a document request
     */
    public function update(User $user, $documentRequest): bool
    {
        // Registrar staff can update requests
        if ($user->can(Permission::ProcessDocumentRequests->value)) {
            return true;
        }

        // Students can only update their own pending requests
        if ($user->id === $documentRequest->user_id) {
            return $documentRequest->status === 'pending_payment';
        }

        return false;
    }

    /**
     * Determine if the user can delete a document request
     */
    public function delete(User $user, $documentRequest): bool
    {
        // Only system admin and registrar admin can delete
        return $user->hasAnyRole([
            Role::SystemAdmin->value,
            Role::RegistrarAdmin->value,
        ]);
    }

    /**
     * Determine if the user can process a document request
     */
    public function process(User $user): bool
    {
        return $user->can(Permission::ProcessDocumentRequests->value);
    }

    /**
     * Determine if the user can approve a document request
     */
    public function approve(User $user, $documentRequest): bool
    {
        // Only requests in appropriate status can be approved
        if (! in_array($documentRequest->status, ['pending', 'payment_confirmed'])) {
            return false;
        }

        return $user->can(Permission::ApproveDocumentRequests->value);
    }

    /**
     * Determine if the user can reject a document request
     */
    public function reject(User $user): bool
    {
        return $user->can(Permission::RejectDocumentRequests->value);
    }

    /**
     * Determine if the user can generate documents
     */
    public function generate(User $user): bool
    {
        return $user->can(Permission::GenerateDocuments->value);
    }

    /**
     * Determine if the user can issue refunds
     */
    public function refund(User $user, $documentRequest): bool
    {
        // Only paid requests can be refunded
        if (! in_array($documentRequest->status, ['payment_confirmed', 'processing'])) {
            return false;
        }

        return $user->can(Permission::IssueRefunds->value);
    }
}
