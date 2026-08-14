<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'         => 'clinic_name',
                'value'       => 'MediQueue Central Clinic',
                'group'       => 'general',
                'label'       => 'Clinic Name',
                'type'        => 'text',
                'description' => 'The public name of the healthcare facility displayed across tickets and screens.',
            ],
            [
                'key'         => 'clinic_email',
                'value'       => 'contact@mediqueue.test',
                'group'       => 'general',
                'label'       => 'Clinic Contact Email',
                'type'        => 'text',
                'description' => 'Official email address for patient inquiries and automated notifications.',
            ],
            [
                'key'         => 'clinic_phone',
                'value'       => '+60 3-8888 1234',
                'group'       => 'general',
                'label'       => 'Clinic Telephone',
                'type'        => 'text',
                'description' => 'Contact phone number printed on queue tickets.',
            ],
            [
                'key'         => 'clinic_address',
                'value'       => 'Level 3, Medical Arts Tower, Kuala Lumpur',
                'group'       => 'general',
                'label'       => 'Clinic Physical Address',
                'type'        => 'text',
                'description' => 'Location of the clinic outpatient facility.',
            ],
            [
                'key'         => 'operating_hours',
                'value'       => '08:00 AM - 06:00 PM',
                'group'       => 'queue',
                'label'       => 'Operating Hours',
                'type'        => 'text',
                'description' => 'Standard daily hours for accepting digital queue registrations.',
            ],
            [
                'key'         => 'enable_email_alerts',
                'value'       => '1',
                'group'       => 'notifications',
                'label'       => 'Enable Email Notifications',
                'type'        => 'boolean',
                'description' => 'Send automated transactional emails when queue tickets are issued and called.',
            ],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
