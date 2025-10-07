<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medications = [
            // Common hypertension medications
            ['bnf_code' => '0205050A0AA', 'generic_name' => 'Amlodipine', 'brand_name' => 'Istin', 'form' => 'Tablet', 'strength' => '5mg', 'description' => 'Amlodipine 5mg tablets'],
            ['bnf_code' => '0205050A0AB', 'generic_name' => 'Amlodipine', 'brand_name' => 'Istin', 'form' => 'Tablet', 'strength' => '10mg', 'description' => 'Amlodipine 10mg tablets'],
            ['bnf_code' => '0205050B0AA', 'generic_name' => 'Lisinopril', 'brand_name' => 'Zestril', 'form' => 'Tablet', 'strength' => '5mg', 'description' => 'Lisinopril 5mg tablets'],
            ['bnf_code' => '0205050B0AB', 'generic_name' => 'Lisinopril', 'brand_name' => 'Zestril', 'form' => 'Tablet', 'strength' => '10mg', 'description' => 'Lisinopril 10mg tablets'],
            ['bnf_code' => '0205050B0AC', 'generic_name' => 'Lisinopril', 'brand_name' => 'Zestril', 'form' => 'Tablet', 'strength' => '20mg', 'description' => 'Lisinopril 20mg tablets'],
            ['bnf_code' => '0205050C0AA', 'generic_name' => 'Losartan', 'brand_name' => 'Cozaar', 'form' => 'Tablet', 'strength' => '50mg', 'description' => 'Losartan 50mg tablets'],
            ['bnf_code' => '0205050C0AB', 'generic_name' => 'Losartan', 'brand_name' => 'Cozaar', 'form' => 'Tablet', 'strength' => '100mg', 'description' => 'Losartan 100mg tablets'],
            ['bnf_code' => '0205050D0AA', 'generic_name' => 'Ramipril', 'brand_name' => 'Tritace', 'form' => 'Capsule', 'strength' => '2.5mg', 'description' => 'Ramipril 2.5mg capsules'],
            ['bnf_code' => '0205050D0AB', 'generic_name' => 'Ramipril', 'brand_name' => 'Tritace', 'form' => 'Capsule', 'strength' => '5mg', 'description' => 'Ramipril 5mg capsules'],
            ['bnf_code' => '0205050D0AC', 'generic_name' => 'Ramipril', 'brand_name' => 'Tritace', 'form' => 'Capsule', 'strength' => '10mg', 'description' => 'Ramipril 10mg capsules'],
            ['bnf_code' => '0205050E0AA', 'generic_name' => 'Bendroflumethiazide', 'brand_name' => 'Aprinox', 'form' => 'Tablet', 'strength' => '2.5mg', 'description' => 'Bendroflumethiazide 2.5mg tablets'],
            ['bnf_code' => '0205050E0AB', 'generic_name' => 'Bendroflumethiazide', 'brand_name' => 'Aprinox', 'form' => 'Tablet', 'strength' => '5mg', 'description' => 'Bendroflumethiazide 5mg tablets'],
            ['bnf_code' => '0205050F0AA', 'generic_name' => 'Indapamide', 'brand_name' => 'Natrilix', 'form' => 'Tablet', 'strength' => '2.5mg', 'description' => 'Indapamide 2.5mg tablets'],
            ['bnf_code' => '0205050G0AA', 'generic_name' => 'Metoprolol', 'brand_name' => 'Betaloc', 'form' => 'Tablet', 'strength' => '50mg', 'description' => 'Metoprolol 50mg tablets'],
            ['bnf_code' => '0205050G0AB', 'generic_name' => 'Metoprolol', 'brand_name' => 'Betaloc', 'form' => 'Tablet', 'strength' => '100mg', 'description' => 'Metoprolol 100mg tablets'],
            ['bnf_code' => '0205050H0AA', 'generic_name' => 'Bisoprolol', 'brand_name' => 'Cardicor', 'form' => 'Tablet', 'strength' => '2.5mg', 'description' => 'Bisoprolol 2.5mg tablets'],
            ['bnf_code' => '0205050H0AB', 'generic_name' => 'Bisoprolol', 'brand_name' => 'Cardicor', 'form' => 'Tablet', 'strength' => '5mg', 'description' => 'Bisoprolol 5mg tablets'],
            ['bnf_code' => '0205050H0AC', 'generic_name' => 'Bisoprolol', 'brand_name' => 'Cardicor', 'form' => 'Tablet', 'strength' => '10mg', 'description' => 'Bisoprolol 10mg tablets'],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
}