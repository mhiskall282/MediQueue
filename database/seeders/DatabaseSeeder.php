<?php

namespace Database\Seeders;

use App\Models\QueueEntry;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Creates:
     * - 1 Admin account
     * - 3 Staff accounts
     * - 5 Patient accounts
     * - 5 Clinic services
     * - Demo queue entries (historical + current)
     *
     * Demo credentials are documented in docs/deployment.md and README.md.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            SettingSeeder::class,
            BedSeeder::class,
            DemoQueueSeeder::class,
        ]);
    }
}
