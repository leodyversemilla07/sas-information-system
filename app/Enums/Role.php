<?php

namespace App\Enums;

/**
 * Role Enum
 *
 * Defines all user roles in the system for type-safety and consistency.
 * Based on DATA_MODELS.md specifications.
 */
enum Role: string
{
    /**
     * Student Role
     * Access: Own records only
     * Permissions: Submit applications, request documents, view own data
     */
    case Student = 'student';

    /**
     * SAS Staff Role
     * Access: SAS module
     * Permissions: Review scholarships, manage organizations, create events
     */
    case SasStaff = 'sas_staff';

    /**
     * SAS Admin Role
     * Access: SAS module + admin functions
     * Permissions: All SAS permissions + approve high-value scholarships (≥₱20k)
     */
    case SasAdmin = 'sas_admin';

    /**
     * Registrar Staff Role
     * Access: Registrar module
     * Permissions: Process document requests, view payments
     */
    case RegistrarStaff = 'registrar_staff';

    /**
     * Registrar Admin Role
     * Access: Registrar module + admin functions
     * Permissions: All registrar permissions + refunds, manual reconciliation
     */
    case RegistrarAdmin = 'registrar_admin';

    /**
     * USG Officer Role
     * Access: USG content management
     * Permissions: Manage announcements, resolutions, view calendar
     */
    case UsgOfficer = 'usg_officer';

    /**
     * USG Admin Role
     * Access: USG module + admin functions
     * Permissions: All USG permissions + manage VMGO, officers, user management
     */
    case UsgAdmin = 'usg_admin';

    /**
     * System Admin Role
     * Access: All modules
     * Permissions: Full system access, manage users, system configuration
     */
    case SystemAdmin = 'system_admin';

    /**
     * Get the role display name
     */
    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::SasStaff => 'SAS Staff',
            self::SasAdmin => 'SAS Administrator',
            self::RegistrarStaff => 'Registrar Staff',
            self::RegistrarAdmin => 'Registrar Administrator',
            self::UsgOfficer => 'USG Officer',
            self::UsgAdmin => 'USG Administrator',
            self::SystemAdmin => 'System Administrator',
        };
    }

    /**
     * Get role description
     */
    public function description(): string
    {
        return match ($this) {
            self::Student => 'Student with access to submit applications and request documents',
            self::SasStaff => 'Student Affairs staff with access to review and manage scholarships',
            self::SasAdmin => 'Student Affairs administrator with full SAS module access',
            self::RegistrarStaff => 'Registrar staff with access to process document requests',
            self::RegistrarAdmin => 'Registrar administrator with full registrar module access',
            self::UsgOfficer => 'USG officer with access to manage announcements and resolutions',
            self::UsgAdmin => 'USG administrator with full USG module access',
            self::SystemAdmin => 'System administrator with full access to all modules',
        };
    }

    /**
     * Get all staff roles (non-student roles)
     *
     * @return array<Role>
     */
    public static function staffRoles(): array
    {
        return [
            self::SasStaff,
            self::SasAdmin,
            self::RegistrarStaff,
            self::RegistrarAdmin,
            self::UsgOfficer,
            self::UsgAdmin,
            self::SystemAdmin,
        ];
    }

    /**
     * Get all admin roles
     *
     * @return array<Role>
     */
    public static function adminRoles(): array
    {
        return [
            self::SasAdmin,
            self::RegistrarAdmin,
            self::UsgAdmin,
            self::SystemAdmin,
        ];
    }

    /**
     * Get SAS module roles
     *
     * @return array<Role>
     */
    public static function sasRoles(): array
    {
        return [
            self::SasStaff,
            self::SasAdmin,
        ];
    }

    /**
     * Get Registrar module roles
     *
     * @return array<Role>
     */
    public static function registrarRoles(): array
    {
        return [
            self::RegistrarStaff,
            self::RegistrarAdmin,
        ];
    }

    /**
     * Get USG module roles
     *
     * @return array<Role>
     */
    public static function usgRoles(): array
    {
        return [
            self::UsgOfficer,
            self::UsgAdmin,
        ];
    }

    /**
     * Check if role has admin privileges
     */
    public function isAdmin(): bool
    {
        return in_array($this, self::adminRoles());
    }

    /**
     * Check if role is system admin
     */
    public function isSystemAdmin(): bool
    {
        return $this === self::SystemAdmin;
    }

    /**
     * Check if role is a student
     */
    public function isStudent(): bool
    {
        return $this === self::Student;
    }
}
