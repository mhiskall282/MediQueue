<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder — Creates default accounts for demonstration.
 *
 * IMPORTANT: These credentials are for DEMONSTRATION ONLY.
 * In a real production deployment, change all passwords and never commit
 * actual credentials to version control.
 *
 * Demo Credentials:
 * ------------------
 * Admin:    admin@mediqueue.test     / password
 * Staff:    dr.sarah@mediqueue.test  / password
 * Staff:    nurse.james@mediqueue.test / password
 * Staff:    dr.chen@mediqueue.test   / password
 * Patient:  john.doe@example.com    / password
 * Patient:  jane.smith@example.com  / password
 * Patient:  ali.hassan@example.com  / password
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@mediqueue.test'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '+601234567890',
                'is_active'=> true,
            ]
        );

        // Staff accounts
        $staffAccounts = [
            [
                'name'  => 'Dr. Sarah Ahmad',
                'email' => 'dr.sarah@mediqueue.test',
                'phone' => '+601234567891',
            ],
            [
                'name'  => 'Nurse James Wong',
                'email' => 'nurse.james@mediqueue.test',
                'phone' => '+601234567892',
            ],
            [
                'name'  => 'Dr. Chen Wei',
                'email' => 'dr.chen@mediqueue.test',
                'phone' => '+601234567893',
            ],
        ];

        foreach ($staffAccounts as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                array_merge($staff, [
                    'password' => Hash::make('password'),
                    'role'     => 'staff',
                    'is_active'=> true,
                ])
            );
        }

        // Patient accounts
        $patients = [
            ['name' => 'John Doe',      'email' => 'john.doe@example.com',    'phone' => '+601111234567'],
            ['name' => 'Jane Smith',    'email' => 'jane.smith@example.com',  'phone' => '+601122345678'],
            ['name' => 'Ali Hassan',    'email' => 'ali.hassan@example.com',  'phone' => '+601133456789'],
            ['name' => 'Siti Aminah',   'email' => 'siti.aminah@example.com', 'phone' => '+601144567890'],
            ['name' => 'Raju Kumar',    'email' => 'raju.kumar@example.com',  'phone' => '+601155678901'],
        ];

        foreach ($patients as $patient) {
            User::updateOrCreate(
                ['email' => $patient['email']],
                array_merge($patient, [
                    'password' => Hash::make('password'),
                    'role'     => 'patient',
                    'is_active'=> true,
                ])
            );
        }
    }
}
