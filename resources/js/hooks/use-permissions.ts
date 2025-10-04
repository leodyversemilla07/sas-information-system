import { usePage } from '@inertiajs/react';
import { SharedData } from '@/types';

/**
 * Hook to access user roles and permissions from Inertia shared data
 *
 * @example
 * const { hasRole, hasPermission, hasAnyRole, hasAllRoles, can, cannot } = usePermissions();
 *
 * if (hasRole('sas_admin')) {
 *   // Show admin controls
 * }
 *
 * if (hasPermission('approve_scholarships_over_20k')) {
 *   // Show approval button
 * }
 */
export function usePermissions() {
    const { auth } = usePage<SharedData>().props;

    /**
     * Check if user has a specific role
     */
    const hasRole = (role: string): boolean => {
        return auth.roles.includes(role);
    };

    /**
     * Check if user has any of the specified roles
     */
    const hasAnyRole = (...roles: string[]): boolean => {
        return roles.some((role) => auth.roles.includes(role));
    };

    /**
     * Check if user has all of the specified roles
     */
    const hasAllRoles = (...roles: string[]): boolean => {
        return roles.every((role) => auth.roles.includes(role));
    };

    /**
     * Check if user has a specific permission
     */
    const hasPermission = (permission: string): boolean => {
        return auth.permissions.includes(permission);
    };

    /**
     * Check if user has any of the specified permissions
     */
    const hasAnyPermission = (...permissions: string[]): boolean => {
        return permissions.some((permission) => auth.permissions.includes(permission));
    };

    /**
     * Check if user has all of the specified permissions
     */
    const hasAllPermissions = (...permissions: string[]): boolean => {
        return permissions.every((permission) => auth.permissions.includes(permission));
    };

    /**
     * Alias for hasPermission (Laravel-style naming)
     */
    const can = (permission: string): boolean => {
        return hasPermission(permission);
    };

    /**
     * Inverse of hasPermission
     */
    const cannot = (permission: string): boolean => {
        return !hasPermission(permission);
    };

    /**
     * Check if user is a student
     */
    const isStudent = (): boolean => {
        return hasRole('student');
    };

    /**
     * Check if user has any SAS role (staff or admin)
     */
    const isSasStaff = (): boolean => {
        return hasAnyRole('sas_staff', 'sas_admin');
    };

    /**
     * Check if user is SAS admin
     */
    const isSasAdmin = (): boolean => {
        return hasRole('sas_admin');
    };

    /**
     * Check if user has any Registrar role (staff or admin)
     */
    const isRegistrarStaff = (): boolean => {
        return hasAnyRole('registrar_staff', 'registrar_admin');
    };

    /**
     * Check if user is Registrar admin
     */
    const isRegistrarAdmin = (): boolean => {
        return hasRole('registrar_admin');
    };

    /**
     * Check if user has any USG role (officer or admin)
     */
    const isUsgStaff = (): boolean => {
        return hasAnyRole('usg_officer', 'usg_admin');
    };

    /**
     * Check if user is USG admin
     */
    const isUsgAdmin = (): boolean => {
        return hasRole('usg_admin');
    };

    /**
     * Check if user is system admin
     */
    const isSystemAdmin = (): boolean => {
        return hasRole('system_admin');
    };

    /**
     * Check if user has any admin role in any module
     */
    const isAnyAdmin = (): boolean => {
        return hasAnyRole('sas_admin', 'registrar_admin', 'usg_admin', 'system_admin');
    };

    return {
        // Core permission checks
        hasRole,
        hasAnyRole,
        hasAllRoles,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        can,
        cannot,

        // Role convenience methods
        isStudent,
        isSasStaff,
        isSasAdmin,
        isRegistrarStaff,
        isRegistrarAdmin,
        isUsgStaff,
        isUsgAdmin,
        isSystemAdmin,
        isAnyAdmin,

        // Direct access to roles and permissions arrays
        roles: auth.roles,
        permissions: auth.permissions,
    };
}
