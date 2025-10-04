<?php

use App\Models\User;
use App\Policies\SAS\ScholarshipPolicy;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    $this->policy = new ScholarshipPolicy;
});

describe('ScholarshipPolicy - View Operations', function () {
    it('allows students to view their own applications', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) ['student_id' => $student->id];

        expect($this->policy->view($student, $scholarship))->toBeTrue();
    });

    it('denies students from viewing other students applications', function () {
        $student = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();
        $scholarship = (object) ['student_id' => $otherStudent->id];

        expect($this->policy->view($student, $scholarship))->toBeFalse();
    });

    it('allows sas staff to view all applications', function () {
        $staff = User::factory()->sasStaff()->create();
        $student = User::factory()->student()->create();
        $scholarship = (object) ['student_id' => $student->id];

        expect($this->policy->viewAny($staff))->toBeTrue()
            ->and($this->policy->view($staff, $scholarship))->toBeTrue();
    });

    it('allows sas admin to view all applications', function () {
        $admin = User::factory()->sasAdmin()->create();
        $student = User::factory()->student()->create();
        $scholarship = (object) ['student_id' => $student->id];

        expect($this->policy->viewAny($admin))->toBeTrue()
            ->and($this->policy->view($admin, $scholarship))->toBeTrue();
    });
});

describe('ScholarshipPolicy - Create Operations', function () {
    it('allows students to create scholarship applications', function () {
        $student = User::factory()->student()->create();

        expect($this->policy->create($student))->toBeTrue();
    });

    it('denies non-students from creating applications on behalf of students', function () {
        $staff = User::factory()->sasStaff()->create();

        expect($this->policy->create($staff))->toBeFalse();
    });
});

describe('ScholarshipPolicy - Update Operations', function () {
    it('allows students to update their pending applications', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'pending_review',
        ];

        expect($this->policy->update($student, $scholarship))->toBeTrue();
    });

    it('denies students from updating approved applications', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'approved',
        ];

        expect($this->policy->update($student, $scholarship))->toBeFalse();
    });

    it('allows sas staff to update applications', function () {
        $staff = User::factory()->sasStaff()->create();
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'pending_review',
        ];

        expect($this->policy->update($staff, $scholarship))->toBeTrue();
    });
});

describe('ScholarshipPolicy - Delete Operations', function () {
    it('allows students to delete their pending applications', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'pending_review',
        ];

        expect($this->policy->delete($student, $scholarship))->toBeTrue();
    });

    it('denies students from deleting approved applications', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'approved',
        ];

        expect($this->policy->delete($student, $scholarship))->toBeFalse();
    });

    it('allows sas admin to delete any application', function () {
        $admin = User::factory()->sasAdmin()->create();
        $student = User::factory()->student()->create();
        $scholarship = (object) [
            'student_id' => $student->id,
            'status' => 'approved',
        ];

        expect($this->policy->delete($admin, $scholarship))->toBeTrue();
    });
});

describe('ScholarshipPolicy - Approval with Amount Threshold', function () {
    it('allows sas staff to approve scholarships under 20k', function () {
        $staff = User::factory()->sasStaff()->create();
        $scholarship = (object) ['amount' => 15000]; // Under ₱20k

        expect($this->policy->approve($staff, $scholarship))->toBeTrue();
    });

    it('denies sas staff from approving scholarships 20k and above', function () {
        $staff = User::factory()->sasStaff()->create();
        $scholarship = (object) ['amount' => 20000]; // Exactly ₱20k

        expect($this->policy->approve($staff, $scholarship))->toBeFalse();
    });

    it('denies sas staff from approving scholarships over 20k', function () {
        $staff = User::factory()->sasStaff()->create();
        $scholarship = (object) ['amount' => 25000]; // Over ₱20k

        expect($this->policy->approve($staff, $scholarship))->toBeFalse();
    });

    it('allows sas admin to approve scholarships under 20k', function () {
        $admin = User::factory()->sasAdmin()->create();
        $scholarship = (object) ['amount' => 15000]; // Under ₱20k

        expect($this->policy->approve($admin, $scholarship))->toBeTrue();
    });

    it('allows sas admin to approve scholarships 20k and above', function () {
        $admin = User::factory()->sasAdmin()->create();
        $scholarship = (object) ['amount' => 20000]; // Exactly ₱20k

        expect($this->policy->approve($admin, $scholarship))->toBeTrue();
    });

    it('allows sas admin to approve scholarships over 20k', function () {
        $admin = User::factory()->sasAdmin()->create();
        $scholarship = (object) ['amount' => 50000]; // Over ₱20k

        expect($this->policy->approve($admin, $scholarship))->toBeTrue();
    });

    it('denies students from approving any scholarships', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) ['amount' => 10000];

        expect($this->policy->approve($student, $scholarship))->toBeFalse();
    });
});

describe('ScholarshipPolicy - Review Operations', function () {
    it('allows sas staff to review scholarships', function () {
        $staff = User::factory()->sasStaff()->create();
        $scholarship = (object) ['student_id' => 123];

        expect($this->policy->review($staff, $scholarship))->toBeTrue();
    });

    it('allows sas admin to review scholarships', function () {
        $admin = User::factory()->sasAdmin()->create();
        $scholarship = (object) ['student_id' => 123];

        expect($this->policy->review($admin, $scholarship))->toBeTrue();
    });

    it('denies students from reviewing scholarships', function () {
        $student = User::factory()->student()->create();
        $scholarship = (object) ['student_id' => 123];

        expect($this->policy->review($student, $scholarship))->toBeFalse();
    });
});
