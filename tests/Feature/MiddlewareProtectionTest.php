<?php

use App\Models\User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
});

describe('SAS Routes Middleware Protection', function () {
    it('allows students to access scholarship application routes', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/sas/scholarships/my-applications');

        $response->assertSuccessful();
    });

    it('allows sas staff to access staff dashboard', function () {
        $staff = User::factory()->sasStaff()->create();

        $response = $this->actingAs($staff)->get('/sas/dashboard');

        $response->assertSuccessful();
    });

    it('allows sas admin to access staff dashboard', function () {
        $admin = User::factory()->sasAdmin()->create();

        $response = $this->actingAs($admin)->get('/sas/dashboard');

        $response->assertSuccessful();
    });

    it('denies students from accessing sas staff dashboard', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/sas/dashboard');

        $response->assertForbidden();
    });

    it('denies students from accessing sas reports', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/sas/reports/scholarships');

        $response->assertForbidden();
    });

    it('allows sas admin to access reports', function () {
        $admin = User::factory()->sasAdmin()->create();

        $response = $this->actingAs($admin)->get('/sas/reports/scholarships');

        $response->assertSuccessful();
    });

    it('denies sas staff from accessing admin-only reports', function () {
        $staff = User::factory()->sasStaff()->create();

        $response = $this->actingAs($staff)->get('/sas/reports/scholarships');

        $response->assertForbidden();
    });
});

describe('Registrar Routes Middleware Protection', function () {
    it('allows students to access document request routes', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/registrar/requests');

        $response->assertSuccessful();
    });

    it('allows registrar staff to access staff dashboard', function () {
        $staff = User::factory()->registrarStaff()->create();

        $response = $this->actingAs($staff)->get('/registrar/dashboard');

        $response->assertSuccessful();
    });

    it('allows registrar admin to access staff dashboard', function () {
        $admin = User::factory()->registrarAdmin()->create();

        $response = $this->actingAs($admin)->get('/registrar/dashboard');

        $response->assertSuccessful();
    });

    it('denies students from accessing registrar staff dashboard', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/registrar/dashboard');

        $response->assertForbidden();
    });

    it('denies registrar staff from accessing admin enrollment routes', function () {
        $staff = User::factory()->registrarStaff()->create();

        $response = $this->actingAs($staff)->get('/registrar/enrollment');

        $response->assertForbidden();
    });

    it('allows registrar admin to access enrollment management', function () {
        $admin = User::factory()->registrarAdmin()->create();

        $response = $this->actingAs($admin)->get('/registrar/enrollment');

        $response->assertSuccessful();
    });
});

describe('USG Routes Middleware Protection', function () {
    it('allows authenticated users to view public announcements', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/usg/announcements');

        $response->assertSuccessful();
    });

    it('allows usg officer to access officer dashboard', function () {
        $officer = User::factory()->usgOfficer()->create();

        $response = $this->actingAs($officer)->get('/usg/dashboard');

        $response->assertSuccessful();
    });

    it('allows usg admin to access admin dashboard', function () {
        $admin = User::factory()->usgAdmin()->create();

        $response = $this->actingAs($admin)->get('/usg/dashboard');

        $response->assertSuccessful();
    });

    it('denies students from accessing usg management routes', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/usg/manage/announcements');

        $response->assertForbidden();
    });

    it('denies usg officer from accessing admin-only routes', function () {
        $officer = User::factory()->usgOfficer()->create();

        $response = $this->actingAs($officer)->get('/usg/admin/vmgo/edit');

        $response->assertForbidden();
    });

    it('allows usg admin to access vmgo management', function () {
        $admin = User::factory()->usgAdmin()->create();

        $response = $this->actingAs($admin)->get('/usg/admin/vmgo/edit');

        $response->assertSuccessful();
    });
});

describe('System Admin Routes Middleware Protection', function () {
    it('allows system admin to access admin dashboard', function () {
        $admin = User::factory()->systemAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSuccessful();
    });

    it('allows system admin to access user management', function () {
        $admin = User::factory()->systemAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertSuccessful();
    });

    it('allows system admin to access role management', function () {
        $admin = User::factory()->systemAdmin()->create();

        $response = $this->actingAs($admin)->get('/admin/roles');

        $response->assertSuccessful();
    });

    it('denies non-system-admin from accessing admin routes', function () {
        $staff = User::factory()->sasAdmin()->create(); // Module admin, not system admin

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        $response->assertForbidden();
    });

    it('denies students from accessing admin routes', function () {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/users');

        $response->assertForbidden();
    });
});

describe('Guest Access Restrictions', function () {
    it('redirects unauthenticated users from protected routes', function () {
        $response = $this->get('/sas/scholarships/my-applications');

        $response->assertRedirect('/login');
    });

    it('redirects unauthenticated users from staff routes', function () {
        $response = $this->get('/sas/dashboard');

        $response->assertRedirect('/login');
    });

    it('redirects unauthenticated users from admin routes', function () {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    });
});
