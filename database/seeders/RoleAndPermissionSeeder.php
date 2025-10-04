<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creating permissions...');
        $this->createPermissions();

        $this->command->info('Creating roles...');
        $this->createRoles();

        $this->command->info('Assigning permissions to roles...');
        $this->assignPermissionsToRoles();

        $this->command->info('✓ Roles and permissions seeded successfully!');
    }

    /**
     * Create all permissions from the Permission enum
     */
    private function createPermissions(): void
    {
        foreach (Permission::cases() as $permission) {
            PermissionModel::firstOrCreate(
                ['name' => $permission->value],
                ['guard_name' => 'web']
            );
        }
    }

    /**
     * Create all roles from the Role enum
     */
    private function createRoles(): void
    {
        foreach (Role::cases() as $role) {
            RoleModel::firstOrCreate(
                ['name' => $role->value],
                ['guard_name' => 'web']
            );
        }
    }

    /**
     * Assign permissions to each role based on their access requirements
     */
    private function assignPermissionsToRoles(): void
    {
        // Student Role - Basic access to submit and view own data
        $this->assignPermissionsTo(Role::Student, [
            Permission::SubmitScholarshipApplication,
            Permission::ViewOwnScholarships,
            Permission::ViewOwnInsuranceRecords,
            Permission::SubmitInsuranceRecords,
            Permission::RequestDocuments,
            Permission::ViewOwnDocumentRequests,
            Permission::MakePayments,
            Permission::ViewOwnPayments,
            Permission::ViewOwnRecords,
            Permission::ViewVmgo,
            Permission::ViewOfficers,
            Permission::ViewAnnouncements,
            Permission::ViewResolutions,
            Permission::ViewUsgCalendar,
            Permission::ViewEvents,
            Permission::ViewOrganizations,
        ]);

        // SAS Staff - Review and manage SAS module
        $this->assignPermissionsTo(Role::SasStaff, [
            // Scholarship management
            Permission::ViewAllScholarships,
            Permission::ReviewScholarships,
            Permission::ApproveScholarshipsUnder20k, // Can only approve under ₱20k
            Permission::RejectScholarships,

            // Organization management
            Permission::ViewOrganizations,
            Permission::ManageOrganizations,
            Permission::CreateOrganizations,
            Permission::UpdateOrganizations,
            Permission::ApproveOrganizations,

            // Event management
            Permission::ViewEvents,
            Permission::CreateEvents,
            Permission::ManageEvents,
            Permission::UpdateEvents,
            Permission::PublishEvents,

            // Insurance records
            Permission::ViewAllInsuranceRecords,
            Permission::ManageInsuranceRecords,
            Permission::ApproveInsuranceRecords,

            // Module access
            Permission::AccessSasModule,
        ]);

        // SAS Admin - Full SAS access including high-value approvals
        $this->assignPermissionsTo(Role::SasAdmin, [
            // All SAS Staff permissions
            ...$this->getRolePermissions(Role::SasStaff),

            // Additional admin permissions
            Permission::ApproveScholarshipsOver20k, // Can approve ≥₱20k
            Permission::DisbursScholarships,
            Permission::DeleteOrganizations,
            Permission::DeleteEvents,
            Permission::ViewSasReports,
        ]);

        // Registrar Staff - Process document requests
        $this->assignPermissionsTo(Role::RegistrarStaff, [
            Permission::ViewAllDocumentRequests,
            Permission::ProcessDocumentRequests,
            Permission::ApproveDocumentRequests,
            Permission::RejectDocumentRequests,
            Permission::GenerateDocuments,
            Permission::ViewAllPayments,
            Permission::ProcessPayments,
            Permission::AccessRegistrarModule,
        ]);

        // Registrar Admin - Full registrar access including refunds
        $this->assignPermissionsTo(Role::RegistrarAdmin, [
            // All Registrar Staff permissions
            ...$this->getRolePermissions(Role::RegistrarStaff),

            // Additional admin permissions
            Permission::IssueRefunds,
            Permission::ManualReconciliation,
            Permission::ViewRegistrarReports,
        ]);

        // USG Officer - Manage public content
        $this->assignPermissionsTo(Role::UsgOfficer, [
            Permission::ViewVmgo,
            Permission::ViewOfficers,
            Permission::ViewAnnouncements,
            Permission::ManageAnnouncements,
            Permission::CreateAnnouncements,
            Permission::UpdateAnnouncements,
            Permission::PublishAnnouncements,
            Permission::ViewResolutions,
            Permission::ManageResolutions,
            Permission::CreateResolutions,
            Permission::UpdateResolutions,
            Permission::PublishResolutions,
            Permission::ViewUsgCalendar,
            Permission::AccessUsgModule,
        ]);

        // USG Admin - Full USG access including VMGO and officers
        $this->assignPermissionsTo(Role::UsgAdmin, [
            // All USG Officer permissions
            ...$this->getRolePermissions(Role::UsgOfficer),

            // Additional admin permissions
            Permission::ManageVmgo,
            Permission::UpdateVmgo,
            Permission::ManageOfficers,
            Permission::CreateOfficers,
            Permission::UpdateOfficers,
            Permission::DeleteOfficers,
            Permission::DeleteAnnouncements,
            Permission::DeleteResolutions,
            Permission::ManageUsgCalendar,
            Permission::AccessUsgAdmin,
        ]);

        // System Admin - Full system access
        $this->assignPermissionsTo(Role::SystemAdmin, [
            // All permissions from all modules
            ...Permission::sasPermissions(),
            ...Permission::registrarPermissions(),
            ...Permission::usgPermissions(),
            ...Permission::systemPermissions(),
        ]);
    }

    /**
     * Assign an array of permissions to a role
     *
     * @param  array<Permission>  $permissions
     */
    private function assignPermissionsTo(Role $role, array $permissions): void
    {
        $roleModel = RoleModel::findByName($role->value);

        $permissionNames = array_map(
            fn ($permission) => $permission instanceof Permission ? $permission->value : $permission,
            $permissions
        );

        $roleModel->syncPermissions($permissionNames);

        $this->command->info('  ✓ Assigned '.count($permissionNames)." permissions to {$role->label()}");
    }

    /**
     * Get all permissions currently assigned to a role
     *
     * @return array<Permission>
     */
    private function getRolePermissions(Role $role): array
    {
        $roleModel = RoleModel::findByName($role->value);
        $permissions = $roleModel->permissions->pluck('name')->toArray();

        return array_filter(
            Permission::cases(),
            fn ($permission) => in_array($permission->value, $permissions)
        );
    }
}
