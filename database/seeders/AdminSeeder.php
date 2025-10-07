<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@myhomebp.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@myhomebp.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create additional admin users
        $additionalAdmins = [
            [
                'name' => 'System Administrator',
                'email' => 'system@myhomebp.com',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'Clinic Manager',
                'email' => 'manager@myhomebp.com',
                'password' => 'admin123',
                'role' => 'admin',
            ],
        ];

        foreach ($additionalAdmins as $adminData) {
            User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'role' => $adminData['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin credentials:');
        $this->command->info('Email: admin@myhomebp.com | Password: admin123');
        $this->command->info('Email: system@myhomebp.com | Password: admin123');
        $this->command->info('Email: manager@myhomebp.com | Password: admin123');
    }
}
