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
            EthnicityCodeSeeder::class,
            MedicationSeeder::class,
            PatientSeeder::class,
            BloodPressureReadingSeeder::class,
        ]);
    }
}