<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            [
                'name' => 'Cardiology',
                'description' => 'Heart and cardiovascular system specialist',
                'code' => 'CARD',
                'is_active' => true,
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Skin, hair, and nail conditions specialist',
                'code' => 'DERM',
                'is_active' => true,
            ],
            [
                'name' => 'Endocrinology',
                'description' => 'Hormone and metabolic disorders specialist',
                'code' => 'ENDO',
                'is_active' => true,
            ],
            [
                'name' => 'Gastroenterology',
                'description' => 'Digestive system specialist',
                'code' => 'GAST',
                'is_active' => true,
            ],
            [
                'name' => 'General Practice',
                'description' => 'Primary care and general medicine',
                'code' => 'GP',
                'is_active' => true,
            ],
            [
                'name' => 'Geriatrics',
                'description' => 'Elderly care specialist',
                'code' => 'GERI',
                'is_active' => true,
            ],
            [
                'name' => 'Hematology',
                'description' => 'Blood and blood-forming organs specialist',
                'code' => 'HEMA',
                'is_active' => true,
            ],
            [
                'name' => 'Nephrology',
                'description' => 'Kidney and urinary system specialist',
                'code' => 'NEPH',
                'is_active' => true,
            ],
            [
                'name' => 'Neurology',
                'description' => 'Nervous system specialist',
                'code' => 'NEUR',
                'is_active' => true,
            ],
            [
                'name' => 'Oncology',
                'description' => 'Cancer treatment specialist',
                'code' => 'ONCO',
                'is_active' => true,
            ],
            [
                'name' => 'Ophthalmology',
                'description' => 'Eye and vision specialist',
                'code' => 'OPHT',
                'is_active' => true,
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Bone, joint, and muscle specialist',
                'code' => 'ORTH',
                'is_active' => true,
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Children\'s health specialist',
                'code' => 'PEDI',
                'is_active' => true,
            ],
            [
                'name' => 'Psychiatry',
                'description' => 'Mental health specialist',
                'code' => 'PSYC',
                'is_active' => true,
            ],
            [
                'name' => 'Pulmonology',
                'description' => 'Lung and respiratory system specialist',
                'code' => 'PULM',
                'is_active' => true,
            ],
            [
                'name' => 'Radiology',
                'description' => 'Medical imaging specialist',
                'code' => 'RADI',
                'is_active' => true,
            ],
            [
                'name' => 'Rheumatology',
                'description' => 'Joint and autoimmune disease specialist',
                'code' => 'RHEU',
                'is_active' => true,
            ],
            [
                'name' => 'Urology',
                'description' => 'Urinary tract and male reproductive system specialist',
                'code' => 'UROL',
                'is_active' => true,
            ],
        ];

        foreach ($specialities as $speciality) {
            Speciality::create($speciality);
        }
    }
}