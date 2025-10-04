<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */


    public function run(): void
    {

        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Define the roles we need
        $roles = [
            'student',
            'sas_staff',
            'sas_admin',
            'registrar_staff',
            'usg_president',
            'admin',
        ];

        // Create roles if they don't exist
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Users with assigned roles
        $users = [
            [
                'name' => 'Student Account',
                'email' => 'student@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'student',
            ],
            [
                'name' => 'SAS Staff',
                'email' => 'sas.staff@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'sas_staff',
            ],
            [
                'name' => 'SAS Admin',
                'email' => 'sas.admin@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'sas_admin',
            ],
            [
                'name' => 'Registrar Staff',
                'email' => 'registrar.staff@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'registrar_staff',
            ],
            [
                'name' => 'USG President',
                'email' => 'usg.president@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'usg_president',
            ],
            [
                'name' => 'System Admin',
                'email' => 'admin@minsu.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );

            // Assign role (avoids duplicates if already assigned)
            if (! $user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
