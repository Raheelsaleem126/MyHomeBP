<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ClinicSeeder::class,
            SpecialitySeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
            BloodPressureReadingSeeder::class,
        ]);
    }
}