<?php

namespace Database\Seeders;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DemoQueueSeeder — Creates realistic demo queue data.
 * Provides a mix of completed (historical) and active (today) entries.
 */
class DemoQueueSeeder extends Seeder
{
    public function run(): void
    {
        $patients = User::where('role', 'patient')->get();
        $staff    = User::where('role', 'staff')->first();
        $gc       = Service::where('prefix', 'GC')->first();
        $ns       = Service::where('prefix', 'NS')->first();
        $ph       = Service::where('prefix', 'PH')->first();

        if (!$gc || !$ns || !$ph || $patients->isEmpty() || !$staff) {
            return;
        }

        // --- Create some completed entries from earlier today ---
        $completedData = [
            ['service' => $gc, 'patient' => $patients->get(0), 'seq' => 1, 'wait' => 25],
            ['service' => $gc, 'patient' => $patients->get(1), 'seq' => 2, 'wait' => 38],
            ['service' => $ns, 'patient' => $patients->get(2), 'seq' => 1, 'wait' => 12],
            ['service' => $ph, 'patient' => $patients->get(3), 'seq' => 1, 'wait' => 5],
        ];

        foreach ($completedData as $data) {
            $joinedAt    = now()->subMinutes($data['wait'] + 30);
            $calledAt    = now()->subMinutes($data['wait'] + 15);
            $startedAt   = now()->subMinutes($data['wait']);
            $completedAt = now()->subMinutes($data['wait'] - ($data['service']->avg_duration_minutes));

            QueueEntry::updateOrCreate(
                [
                    'patient_id'     => $data['patient']->id,
                    'service_id'     => $data['service']->id,
                    'sequence_number'=> $data['seq'],
                ],
                [
                    'served_by'          => $staff->id,
                    'queue_number'       => sprintf('%s-%03d', $data['service']->prefix, $data['seq']),
                    'status'             => 'COMPLETED',
                    'priority'           => 'NORMAL',
                    'joined_at'          => $joinedAt,
                    'called_at'          => $calledAt,
                    'service_started_at' => $startedAt,
                    'completed_at'       => $completedAt,
                    'created_at'         => $joinedAt,
                    'updated_at'         => $completedAt,
                ]
            );
        }

        // --- Create current WAITING entries ---
        $waitingData = [
            ['service' => $gc, 'patient' => $patients->get(2), 'seq' => 3],
            ['service' => $gc, 'patient' => $patients->get(3), 'seq' => 4],
            ['service' => $gc, 'patient' => $patients->get(4), 'seq' => 5],
            ['service' => $ns, 'patient' => $patients->get(0), 'seq' => 2],
        ];

        foreach ($waitingData as $data) {
            QueueEntry::updateOrCreate(
                [
                    'patient_id'     => $data['patient']->id,
                    'service_id'     => $data['service']->id,
                    'sequence_number'=> $data['seq'],
                ],
                [
                    'queue_number' => sprintf('%s-%03d', $data['service']->prefix, $data['seq']),
                    'status'       => 'WAITING',
                    'priority'     => 'NORMAL',
                    'joined_at'    => now()->subMinutes(rand(2, 20)),
                ]
            );
        }

        // --- Create a CALLED entry to show staff dashboard in action ---
        QueueEntry::updateOrCreate(
            [
                'patient_id'     => $patients->get(1)->id,
                'service_id'     => $gc->id,
                'sequence_number'=> 3,
            ],
            [
                'served_by'    => $staff->id,
                'queue_number' => 'GC-003',
                'status'       => 'CALLED',
                'priority'     => 'NORMAL',
                'joined_at'    => now()->subMinutes(25),
                'called_at'    => now()->subMinutes(2),
            ]
        );
    }
}
