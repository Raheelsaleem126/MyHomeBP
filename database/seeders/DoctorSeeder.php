<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\Clinic;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'first_name' => 'Dr. Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'phone' => '+44 20 7123 4567',
                'gmc_number' => 'GMC123456',
                'date_of_birth' => '1980-05-15',
                'gender' => 'female',
                'qualifications' => 'MBBS, MRCP, PhD in Cardiology',
                'years_of_experience' => 15,
                'bio' => 'Experienced cardiologist with expertise in interventional cardiology and heart failure management.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Cardiology', 'General Practice'],
                'primary_speciality' => 'Cardiology',
            ],
            [
                'first_name' => 'Dr. Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@example.com',
                'phone' => '+44 20 7123 4568',
                'gmc_number' => 'GMC123457',
                'date_of_birth' => '1975-08-22',
                'gender' => 'male',
                'qualifications' => 'MBBS, MD, FRCP',
                'years_of_experience' => 20,
                'bio' => 'Senior neurologist specializing in movement disorders and epilepsy.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Neurology', 'General Practice'],
                'primary_speciality' => 'Neurology',
            ],
            [
                'first_name' => 'Dr. Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.rodriguez@example.com',
                'phone' => '+44 20 7123 4569',
                'gmc_number' => 'GMC123458',
                'date_of_birth' => '1982-12-10',
                'gender' => 'female',
                'qualifications' => 'MBBS, MRCPCH, DCH',
                'years_of_experience' => 12,
                'bio' => 'Pediatrician with special interest in developmental pediatrics and neonatology.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Pediatrics', 'General Practice'],
                'primary_speciality' => 'Pediatrics',
            ],
            [
                'first_name' => 'Dr. James',
                'last_name' => 'Thompson',
                'email' => 'james.thompson@example.com',
                'phone' => '+44 20 7123 4570',
                'gmc_number' => 'GMC123459',
                'date_of_birth' => '1978-03-18',
                'gender' => 'male',
                'qualifications' => 'MBBS, FRCS, MSc Orthopedics',
                'years_of_experience' => 18,
                'bio' => 'Orthopedic surgeon specializing in joint replacement and sports medicine.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Orthopedics', 'General Practice'],
                'primary_speciality' => 'Orthopedics',
            ],
            [
                'first_name' => 'Dr. Lisa',
                'last_name' => 'Williams',
                'email' => 'lisa.williams@example.com',
                'phone' => '+44 20 7123 4571',
                'gmc_number' => 'GMC123460',
                'date_of_birth' => '1985-07-05',
                'gender' => 'female',
                'qualifications' => 'MBBS, MRCP, MD Endocrinology',
                'years_of_experience' => 10,
                'bio' => 'Endocrinologist with expertise in diabetes management and thyroid disorders.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Endocrinology', 'General Practice'],
                'primary_speciality' => 'Endocrinology',
            ],
            [
                'first_name' => 'Dr. Robert',
                'last_name' => 'Brown',
                'email' => 'robert.brown@example.com',
                'phone' => '+44 20 7123 4572',
                'gmc_number' => 'GMC123461',
                'date_of_birth' => '1970-11-30',
                'gender' => 'male',
                'qualifications' => 'MBBS, FRCP, PhD Psychiatry',
                'years_of_experience' => 25,
                'bio' => 'Consultant psychiatrist specializing in mood disorders and psychotherapy.',
                'is_active' => true,
                'is_available' => true,
                'specialities' => ['Psychiatry', 'General Practice'],
                'primary_speciality' => 'Psychiatry',
            ],
        ];

        // Get all clinics for assignment
        $clinics = Clinic::all();
        $specialities = Speciality::all();

        foreach ($doctors as $doctorData) {
            $specialityNames = $doctorData['specialities'];
            $primarySpecialityName = $doctorData['primary_speciality'];
            unset($doctorData['specialities'], $doctorData['primary_speciality']);

            // Create the doctor
            $doctor = Doctor::create($doctorData);

            // Attach specialities
            foreach ($specialityNames as $specialityName) {
                $speciality = $specialities->where('name', $specialityName)->first();
                if ($speciality) {
                    $isPrimary = ($specialityName === $primarySpecialityName);
                    $doctor->specialities()->attach($speciality->id, [
                        'is_primary' => $isPrimary,
                        'certification_date' => now()->subYears(rand(5, 15)),
                        'certification_body' => 'GMC',
                    ]);
                }
            }

            // Assign doctor to random clinics (1-3 clinics per doctor)
            // Note: Only 5 clinics are available, so max assignment is limited to available clinics
            $clinicCount = rand(1, min(3, $clinics->count()));
            $selectedClinics = $clinics->random($clinicCount);
            
            foreach ($selectedClinics as $clinic) {
                $doctor->clinics()->attach($clinic->id, [
                    'start_date' => now()->subMonths(rand(1, 24)),
                    'status' => 'active',
                ]);
            }
        }
    }
}