<?php

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }

    // ========================================
    // Role-Specific States
    // ========================================

    /**
     * Create a user with Student role
     */
    public function student(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::Student->value);
        });
    }

    /**
     * Create a user with SAS Staff role
     */
    public function sasStaff(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::SasStaff->value);
        });
    }

    /**
     * Create a user with SAS Admin role
     */
    public function sasAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::SasAdmin->value);
        });
    }

    /**
     * Create a user with Registrar Staff role
     */
    public function registrarStaff(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::RegistrarStaff->value);
        });
    }

    /**
     * Create a user with Registrar Admin role
     */
    public function registrarAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::RegistrarAdmin->value);
        });
    }

    /**
     * Create a user with USG Officer role
     */
    public function usgOfficer(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::UsgOfficer->value);
        });
    }

    /**
     * Create a user with USG Admin role
     */
    public function usgAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::UsgAdmin->value);
        });
    }

    /**
     * Create a user with System Admin role
     */
    public function systemAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole(Role::SystemAdmin->value);
        });
    }

    /**
     * Create a user with a specific role
     */
    public function withRole(Role|string $role): static
    {
        return $this->afterCreating(function ($user) use ($role) {
            $roleName = $role instanceof Role ? $role->value : $role;
            $user->assignRole($roleName);
        });
    }

    /**
     * Create a user with multiple roles
     *
     * @param  array<Role|string>  $roles
     */
    public function withRoles(array $roles): static
    {
        return $this->afterCreating(function ($user) use ($roles) {
            $roleNames = array_map(
                fn ($role) => $role instanceof Role ? $role->value : $role,
                $roles
            );
            $user->assignRole($roleNames);
        });
    }
}
