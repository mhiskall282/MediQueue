<?php

namespace Database\Seeders;

use App\Models\DoctorRoster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@mediqueue.test'],
            [
                'hospital_id'    => 'MED-ADM-00001',
                'name'           => 'System Administrator',
                'password'       => Hash::make('password'),
                'role'           => 'admin',
                'specialization' => 'Healthcare Information Systems',
                'phone'          => '+601234567890',
                'is_active'      => true,
            ]
        );

        // Staff accounts with Medical Specializations & On-Call Status
        $staffAccounts = [
            [
                'hospital_id'             => 'MED-DOC-00101',
                'name'                    => 'Dr. Sarah Ahmad',
                'email'                   => 'dr.sarah@mediqueue.test',
                'phone'                   => '+601234567891',
                'specialization'          => 'Emergency Medicine & Trauma Care',
                'is_on_call'              => true,
                'on_call_shift'           => '24H_TRAUMA',
                'emergency_contact_phone' => '+601234567891',
            ],
            [
                'hospital_id'             => 'MED-NUR-00201',
                'name'                    => 'Nurse James Wong',
                'email'                   => 'nurse.james@mediqueue.test',
                'phone'                   => '+601234567892',
                'specialization'          => 'Critical Care & Triage Assessment',
                'is_on_call'              => true,
                'on_call_shift'           => 'DAY_SHIFT',
                'emergency_contact_phone' => '+601234567892',
            ],
            [
                'hospital_id'             => 'MED-DOC-00102',
                'name'                    => 'Dr. Chen Wei',
                'email'                   => 'dr.chen@mediqueue.test',
                'phone'                   => '+601234567893',
                'specialization'          => 'Cardiology & Intensive Care',
                'is_on_call'              => true,
                'on_call_shift'           => 'NIGHT_EMERGENCY',
                'emergency_contact_phone' => '+601234567893',
            ],
        ];

        foreach ($staffAccounts as $staff) {
            $user = User::updateOrCreate(
                ['email' => $staff['email']],
                array_merge($staff, [
                    'password' => Hash::make('password'),
                    'role'     => 'staff',
                    'is_active'=> true,
                ])
            );

            // Create Doctor Roster for today
            DoctorRoster::firstOrCreate(
                ['doctor_id' => $user->id, 'duty_date' => Carbon::today()],
                [
                    'shift_type' => $staff['on_call_shift'] === 'NIGHT_EMERGENCY' ? DoctorRoster::SHIFT_NIGHT : DoctorRoster::SHIFT_ON_CALL_TRAUMA,
                    'status'     => 'ACTIVE',
                    'duty_notes' => 'On-duty emergency standby clinician',
                ]
            );
        }

        // Patient accounts with Medical Record Numbers (MRN)
        $patients = [
            ['hospital_id' => 'MRN-2026-00001', 'name' => 'John Doe',    'email' => 'john.doe@example.com',    'phone' => '+601111234567'],
            ['hospital_id' => 'MRN-2026-00002', 'name' => 'Jane Smith',  'email' => 'jane.smith@example.com',  'phone' => '+601122345678'],
            ['hospital_id' => 'MRN-2026-00003', 'name' => 'Ali Hassan',  'email' => 'ali.hassan@example.com',  'phone' => '+601133456789'],
            ['hospital_id' => 'MRN-2026-00004', 'name' => 'Siti Aminah', 'email' => 'siti.aminah@example.com', 'phone' => '+601144567890'],
            ['hospital_id' => 'MRN-2026-00005', 'name' => 'Raju Kumar',  'email' => 'raju.kumar@example.com',  'phone' => '+601155678901'],
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
