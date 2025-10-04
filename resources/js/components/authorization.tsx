import { usePermissions } from '@/hooks/use-permissions';
import React from 'react';

interface CanProps {
    permission: string | string[];
    children: React.ReactNode;
    fallback?: React.ReactNode;
}

/**
 * Component to conditionally render content based on permissions
 *
 * @example
 * <Can permission="approve_scholarships_over_20k">
 *   <button>Approve</button>
 * </Can>
 *
 * @example
 * <Can permission={['edit_announcements', 'delete_announcements']}>
 *   <button>Edit</button>
 * </Can>
 */
export function Can({ permission, children, fallback = null }: CanProps) {
    const { hasPermission, hasAnyPermission } = usePermissions();

    const canAccess = Array.isArray(permission)
        ? hasAnyPermission(...permission)
        : hasPermission(permission);

    return <>{canAccess ? children : fallback}</>;
}

interface HasRoleProps {
    role: string | string[];
    children: React.ReactNode;
    fallback?: React.ReactNode;
}

/**
 * Component to conditionally render content based on roles
 *
 * @example
 * <HasRole role="sas_admin">
 *   <AdminPanel />
 * </HasRole>
 *
 * @example
 * <HasRole role={['sas_staff', 'sas_admin']}>
 *   <StaffControls />
 * </HasRole>
 */
export function HasRole({ role, children, fallback = null }: HasRoleProps) {
    const { hasRole, hasAnyRole } = usePermissions();

    const hasRequiredRole = Array.isArray(role)
        ? hasAnyRole(...role)
        : hasRole(role);

    return <>{hasRequiredRole ? children : fallback}</>;
}

interface UnlessProps {
    permission: string;
    children: React.ReactNode;
    fallback?: React.ReactNode;
}

/**
 * Component to conditionally render content when permission is NOT present
 *
 * @example
 * <Unless permission="approve_scholarships_over_20k">
 *   <p>You don't have approval permissions</p>
 * </Unless>
 */
export function Unless({ permission, children, fallback = null }: UnlessProps) {
    const { cannot } = usePermissions();

    return <>{cannot(permission) ? children : fallback}</>;
}

interface HasAllPermissionsProps {
    permissions: string[];
    children: React.ReactNode;
    fallback?: React.ReactNode;
}

/**
 * Component to conditionally render content when ALL permissions are present
 *
 * @example
 * <HasAllPermissions permissions={['edit_announcements', 'publish_announcements']}>
 *   <PublishButton />
 * </HasAllPermissions>
 */
export function HasAllPermissions({
    permissions,
    children,
    fallback = null,
}: HasAllPermissionsProps) {
    const { hasAllPermissions } = usePermissions();

    return <>{hasAllPermissions(...permissions) ? children : fallback}</>;
}
