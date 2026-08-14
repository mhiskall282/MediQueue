<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * ServiceSeeder — Creates realistic clinic services.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'                 => 'General Consultation',
                'description'          => 'See a general practitioner for common illnesses, minor injuries, and medical advice.',
                'prefix'               => 'GC',
                'avg_duration_minutes' => 15,
                'is_active'            => true,
            ],
            [
                'name'                 => 'Nursing Services',
                'description'          => 'Wound dressing, injections, blood pressure monitoring, and basic nursing care.',
                'prefix'               => 'NS',
                'avg_duration_minutes' => 10,
                'is_active'            => true,
            ],
            [
                'name'                 => 'Pharmacy Pickup',
                'description'          => 'Collect prescribed medication from the clinic pharmacy.',
                'prefix'               => 'PH',
                'avg_duration_minutes' => 5,
                'is_active'            => true,
            ],
            [
                'name'                 => 'Laboratory Services',
                'description'          => 'Blood tests, urine analysis, and other diagnostic laboratory procedures.',
                'prefix'               => 'LAB',
                'avg_duration_minutes' => 20,
                'is_active'            => true,
            ],
            [
                'name'                 => 'Health Screening',
                'description'          => 'Annual health screening packages including BMI, blood glucose, and cholesterol checks.',
                'prefix'               => 'HS',
                'avg_duration_minutes' => 30,
                'is_active'            => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['prefix' => $service['prefix']],
                $service
            );
        }
    }
}
