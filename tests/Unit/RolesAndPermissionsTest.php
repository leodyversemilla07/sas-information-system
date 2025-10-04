<?php

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Run the role and permission seeder
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

describe('Role Assignment', function () {
    it('can assign a role to a user', function () {
        $user = User::factory()->create();

        $user->assignRole(Role::Student->value);

        expect($user->hasRole(Role::Student->value))->toBeTrue();
    });

    it('can assign multiple roles to a user', function () {
        $user = User::factory()->create();

        $user->assignRole([Role::Student->value, Role::SasStaff->value]);

        expect($user->hasRole(Role::Student->value))->toBeTrue()
            ->and($user->hasRole(Role::SasStaff->value))->toBeTrue();
    });

    it('can remove a role from a user', function () {
        $user = User::factory()->create();
        $user->assignRole(Role::Student->value);

        $user->removeRole(Role::Student->value);

        expect($user->hasRole(Role::Student->value))->toBeFalse();
    });

    it('can sync roles for a user', function () {
        $user = User::factory()->create();
        $user->assignRole([Role::Student->value, Role::SasStaff->value]);

        $user->syncRoles([Role::SasAdmin->value]);

        expect($user->hasRole(Role::Student->value))->toBeFalse()
            ->and($user->hasRole(Role::SasStaff->value))->toBeFalse()
            ->and($user->hasRole(Role::SasAdmin->value))->toBeTrue();
    });
});

describe('Permission Checks', function () {
    it('student has submit scholarship application permission', function () {
        $user = User::factory()->student()->create();

        expect($user->can('submit_scholarship_application'))->toBeTrue();
    });

    it('sas staff has review scholarships permission', function () {
        $user = User::factory()->sasStaff()->create();

        expect($user->can('review_scholarships'))->toBeTrue();
    });

    it('sas admin has approve scholarships over 20k permission', function () {
        $user = User::factory()->sasAdmin()->create();

        expect($user->can('approve_scholarships_over_20k'))->toBeTrue();
    });

    it('registrar staff has process document requests permission', function () {
        $user = User::factory()->registrarStaff()->create();

        expect($user->can('process_document_requests'))->toBeTrue();
    });

    it('registrar admin has issue refunds permission', function () {
        $user = User::factory()->registrarAdmin()->create();

        expect($user->can('issue_refunds'))->toBeTrue();
    });

    it('usg officer has create announcements permission', function () {
        $user = User::factory()->usgOfficer()->create();

        expect($user->can('create_announcements'))->toBeTrue();
    });

    it('usg admin has manage vmgo permission', function () {
        $user = User::factory()->usgAdmin()->create();

        expect($user->can('manage_vmgo'))->toBeTrue();
    });

    it('system admin has all permissions', function () {
        $user = User::factory()->systemAdmin()->create();

        $allPermissions = Permission::all()->pluck('name');

        foreach ($allPermissions as $permission) {
            expect($user->can($permission))->toBeTrue();
        }
    });

    it('student does not have admin permissions', function () {
        $user = User::factory()->student()->create();

        expect($user->can('approve_scholarships_over_20k'))->toBeFalse()
            ->and($user->can('manage_users'))->toBeFalse()
            ->and($user->can('manage_roles'))->toBeFalse();
    });
});

describe('Role Hierarchy', function () {
    it('sas admin has all sas staff permissions', function () {
        $sasStaff = User::factory()->sasStaff()->create();
        $sasAdmin = User::factory()->sasAdmin()->create();

        $staffPermissions = $sasStaff->getAllPermissions()->pluck('name');

        foreach ($staffPermissions as $permission) {
            expect($sasAdmin->can($permission))->toBeTrue();
        }
    });

    it('registrar admin has all registrar staff permissions', function () {
        $registrarStaff = User::factory()->registrarStaff()->create();
        $registrarAdmin = User::factory()->registrarAdmin()->create();

        $staffPermissions = $registrarStaff->getAllPermissions()->pluck('name');

        foreach ($staffPermissions as $permission) {
            expect($registrarAdmin->can($permission))->toBeTrue();
        }
    });

    it('usg admin has all usg officer permissions', function () {
        $usgOfficer = User::factory()->usgOfficer()->create();
        $usgAdmin = User::factory()->usgAdmin()->create();

        $officerPermissions = $usgOfficer->getAllPermissions()->pluck('name');

        foreach ($officerPermissions as $permission) {
            expect($usgAdmin->can($permission))->toBeTrue();
        }
    });
});

describe('Direct Permission Assignment', function () {
    it('can assign permission directly to a user', function () {
        $user = User::factory()->create();
        $permission = Permission::findByName('submit_scholarship_application');

        $user->givePermissionTo($permission);

        expect($user->hasPermissionTo('submit_scholarship_application'))->toBeTrue();
    });

    it('can revoke permission from a user', function () {
        $user = User::factory()->student()->create();

        $user->revokePermissionTo('submit_scholarship_application');

        expect($user->hasPermissionTo('submit_scholarship_application'))->toBeFalse();
    });
});

describe('Multiple Role Checks', function () {
    it('can check if user has any of multiple roles', function () {
        $user = User::factory()->sasStaff()->create();

        expect($user->hasAnyRole([Role::SasStaff->value, Role::SasAdmin->value]))->toBeTrue()
            ->and($user->hasAnyRole([Role::Student->value, Role::RegistrarStaff->value]))->toBeFalse();
    });

    it('can check if user has all of multiple roles', function () {
        $user = User::factory()->create();
        $user->assignRole([Role::Student->value, Role::SasStaff->value]);

        expect($user->hasAllRoles([Role::Student->value, Role::SasStaff->value]))->toBeTrue()
            ->and($user->hasAllRoles([Role::Student->value, Role::SasAdmin->value]))->toBeFalse();
    });
});

describe('Factory States', function () {
    it('creates student with correct role', function () {
        $user = User::factory()->student()->create();

        expect($user->hasRole(Role::Student->value))->toBeTrue();
    });

    it('creates sas staff with correct role', function () {
        $user = User::factory()->sasStaff()->create();

        expect($user->hasRole(Role::SasStaff->value))->toBeTrue();
    });

    it('creates sas admin with correct role', function () {
        $user = User::factory()->sasAdmin()->create();

        expect($user->hasRole(Role::SasAdmin->value))->toBeTrue();
    });

    it('creates registrar staff with correct role', function () {
        $user = User::factory()->registrarStaff()->create();

        expect($user->hasRole(Role::RegistrarStaff->value))->toBeTrue();
    });

    it('creates registrar admin with correct role', function () {
        $user = User::factory()->registrarAdmin()->create();

        expect($user->hasRole(Role::RegistrarAdmin->value))->toBeTrue();
    });

    it('creates usg officer with correct role', function () {
        $user = User::factory()->usgOfficer()->create();

        expect($user->hasRole(Role::UsgOfficer->value))->toBeTrue();
    });

    it('creates usg admin with correct role', function () {
        $user = User::factory()->usgAdmin()->create();

        expect($user->hasRole(Role::UsgAdmin->value))->toBeTrue();
    });

    it('creates system admin with correct role', function () {
        $user = User::factory()->systemAdmin()->create();

        expect($user->hasRole(Role::SystemAdmin->value))->toBeTrue();
    });
});
