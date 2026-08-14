<?php

namespace Database\Seeders;

use App\Models\Bed;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    /**
     * Seed initial hospital beds and triage bays.
     */
    public function run(): void
    {
        $beds = [
            // Triage Bays
            ['ward_name' => 'Emergency Triage Wing', 'bed_number' => 'TB-01', 'bed_type' => Bed::TYPE_TRIAGE_BAY, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Rapid Assessment Bay 1'],
            ['ward_name' => 'Emergency Triage Wing', 'bed_number' => 'TB-02', 'bed_type' => Bed::TYPE_TRIAGE_BAY, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Rapid Assessment Bay 2'],
            ['ward_name' => 'Emergency Triage Wing', 'bed_number' => 'TB-03', 'bed_type' => Bed::TYPE_TRIAGE_BAY, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Resuscitation Bay'],

            // Observation Ward
            ['ward_name' => 'Observation Ward (Level 1)', 'bed_number' => 'OBS-101', 'bed_type' => Bed::TYPE_OBSERVATION, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Post-consultation monitoring'],
            ['ward_name' => 'Observation Ward (Level 1)', 'bed_number' => 'OBS-102', 'bed_type' => Bed::TYPE_OBSERVATION, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Post-consultation monitoring'],
            ['ward_name' => 'Observation Ward (Level 1)', 'bed_number' => 'OBS-103', 'bed_type' => Bed::TYPE_OBSERVATION, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Post-consultation monitoring'],

            // General Outpatient Ward
            ['ward_name' => 'General Ward A', 'bed_number' => 'GW-201', 'bed_type' => Bed::TYPE_GENERAL, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Standard Care Bed'],
            ['ward_name' => 'General Ward A', 'bed_number' => 'GW-202', 'bed_type' => Bed::TYPE_GENERAL, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Standard Care Bed'],
            ['ward_name' => 'General Ward B', 'bed_number' => 'GW-301', 'bed_type' => Bed::TYPE_GENERAL, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Standard Care Bed'],

            // Intensive Care Unit
            ['ward_name' => 'Intensive Care Unit (ICU)', 'bed_number' => 'ICU-01', 'bed_type' => Bed::TYPE_ICU, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Critical Care Unit with Ventilator Support'],
            ['ward_name' => 'Intensive Care Unit (ICU)', 'bed_number' => 'ICU-02', 'bed_type' => Bed::TYPE_ICU, 'status' => Bed::STATUS_AVAILABLE, 'notes' => 'Critical Care Unit'],
        ];

        foreach ($beds as $bedData) {
            Bed::firstOrCreate(['bed_number' => $bedData['bed_number']], $bedData);
        }
    }
}
