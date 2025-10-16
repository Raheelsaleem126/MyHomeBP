<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Clinic;
use App\Models\ClinicalData;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all clinics to assign patients to
        $clinics = Clinic::all();
        
        if ($clinics->isEmpty()) {
            $this->command->error('No clinics found. Please run ClinicSeeder first.');
            return;
        }

        $patients = [
            [
                'first_name' => 'John',
                'surname' => 'Smith',
                'date_of_birth' => '1985-03-15',
                'address' => '123 Baker Street, London NW1 6XE',
                'mobile_phone' => '07123456789',
                'home_phone' => '02079460958',
                'email' => 'john.smith@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(2),
            ],
            [
                'first_name' => 'Sarah',
                'surname' => 'Johnson',
                'date_of_birth' => '1978-07-22',
                'address' => '45 Victoria Road, Manchester M14 6JA',
                'mobile_phone' => '07987654321',
                'home_phone' => '01612761234',
                'email' => 'sarah.johnson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => false,
                'last_login_at' => Carbon::now()->subDays(1),
            ],
            [
                'first_name' => 'Michael',
                'surname' => 'Brown',
                'date_of_birth' => '1992-11-08',
                'address' => '78 Queen Street, Birmingham B1 1RA',
                'mobile_phone' => '07456789123',
                'home_phone' => '01212002000',
                'email' => 'michael.brown@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => false,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subHours(6),
            ],
            [
                'first_name' => 'Emma',
                'surname' => 'Wilson',
                'date_of_birth' => '1987-05-14',
                'address' => '12 Park Lane, Leeds LS1 3EX',
                'mobile_phone' => '07891234567',
                'home_phone' => '01132432799',
                'email' => 'emma.wilson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(3),
            ],
            [
                'first_name' => 'David',
                'surname' => 'Taylor',
                'date_of_birth' => '1965-12-03',
                'address' => '67 High Street, Glasgow G1 1QZ',
                'mobile_phone' => '07654321987',
                'home_phone' => '01412114000',
                'email' => 'david.taylor@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(7),
            ],
            [
                'first_name' => 'Lisa',
                'surname' => 'Anderson',
                'date_of_birth' => '1990-09-18',
                'address' => '34 Church Street, Liverpool L1 5BX',
                'mobile_phone' => '07345678901',
                'home_phone' => '01517062000',
                'email' => 'lisa.anderson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => false,
                'notifications_consent' => false,
                'last_login_at' => Carbon::now()->subDays(5),
            ],
            [
                'first_name' => 'Robert',
                'surname' => 'Thomas',
                'date_of_birth' => '1982-01-25',
                'address' => '89 Mill Lane, London SE1 7EH',
                'mobile_phone' => '07901234567',
                'home_phone' => '02071887188',
                'email' => 'robert.thomas@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subHours(12),
            ],
            [
                'first_name' => 'Jennifer',
                'surname' => 'Jackson',
                'date_of_birth' => '1975-06-30',
                'address' => '156 Oxford Road, Manchester M13 9WL',
                'mobile_phone' => '07432109876',
                'home_phone' => '01612761234',
                'email' => 'jennifer.jackson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(1),
            ],
            [
                'first_name' => 'Christopher',
                'surname' => 'White',
                'date_of_birth' => '1988-04-12',
                'address' => '23 Bull Street, Birmingham B4 6AD',
                'mobile_phone' => '07678901234',
                'home_phone' => '01212002000',
                'email' => 'christopher.white@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => false,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(4),
            ],
            [
                'first_name' => 'Amanda',
                'surname' => 'Harris',
                'date_of_birth' => '1993-08-07',
                'address' => '45 Briggate, Leeds LS1 6HD',
                'mobile_phone' => '07234567890',
                'home_phone' => '01132432799',
                'email' => 'amanda.harris@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subHours(3),
            ],
            [
                'first_name' => 'James',
                'surname' => 'Martin',
                'date_of_birth' => '1968-10-15',
                'address' => '78 Buchanan Street, Glasgow G1 3HL',
                'mobile_phone' => '07567890123',
                'home_phone' => '01412114000',
                'email' => 'james.martin@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => false,
                'last_login_at' => Carbon::now()->subDays(6),
            ],
            [
                'first_name' => 'Michelle',
                'surname' => 'Thompson',
                'date_of_birth' => '1984-02-28',
                'address' => '12 Bold Street, Liverpool L1 4JA',
                'mobile_phone' => '07890123456',
                'home_phone' => '01517062000',
                'email' => 'michelle.thompson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(2),
            ],
            [
                'first_name' => 'Daniel',
                'surname' => 'Garcia',
                'date_of_birth' => '1991-12-10',
                'address' => '67 Fleet Street, London EC4Y 1HT',
                'mobile_phone' => '07123456780',
                'home_phone' => '02079460958',
                'email' => 'daniel.garcia@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => false,
                'notifications_consent' => false,
                'last_login_at' => Carbon::now()->subDays(8),
            ],
            [
                'first_name' => 'Nicole',
                'surname' => 'Martinez',
                'date_of_birth' => '1979-07-05',
                'address' => '34 Deansgate, Manchester M3 2BW',
                'mobile_phone' => '07987654320',
                'home_phone' => '01612761234',
                'email' => 'nicole.martinez@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subHours(8),
            ],
            [
                'first_name' => 'Kevin',
                'surname' => 'Robinson',
                'date_of_birth' => '1986-11-20',
                'address' => '89 New Street, Birmingham B2 4QA',
                'mobile_phone' => '07456789120',
                'home_phone' => '01212002000',
                'email' => 'kevin.robinson@email.com',
                'pin' => '1234',
                'terms_accepted' => true,
                'data_sharing_consent' => true,
                'notifications_consent' => true,
                'last_login_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($patients as $patientData) {
            // Check if patient already exists
            if (Patient::where('email', $patientData['email'])->exists()) {
                $this->command->info("Patient with email {$patientData['email']} already exists. Skipping...");
                continue;
            }
            
            // Hash the pin
            $patientData['pin'] = Hash::make($patientData['pin']);
            
            // Assign a random clinic
            $patientData['clinic_id'] = $clinics->random()->id;
            
            // Create the patient
            $patient = Patient::create($patientData);
            
            // Create clinical data for each patient (80% chance)
            if (rand(1, 100) <= 80) {
                $this->createClinicalData($patient);
            }
        }

        $this->command->info('Created ' . count($patients) . ' patients successfully.');
    }

    /**
     * Create clinical data for a patient
     */
    private function createClinicalData(Patient $patient): void
    {
        $age = $patient->age;
        
        // Generate realistic clinical data based on age
        $heightCm = rand(150, 190);
        $weightKg = $this->getRealisticWeight($age, $heightCm);
        $bmi = round($weightKg / (($heightCm / 100) ** 2), 1);
        
        $clinicalData = [
            'patient_id' => $patient->id,
            'height' => $heightCm,
            'weight' => $weightKg,
            'bmi' => $bmi,
            'ethnicity_code' => $this->getRandomEthnicityCode(),
            'ethnicity_description' => $this->getRandomEthnicityDescription(),
            'smoking_status' => $this->getRandomSmokingStatus(),
            'hypertension_diagnosis' => rand(1, 100) <= 25 ? 'yes' : 'no', // 25% chance of hypertension
            'medications' => $this->getRandomMedications(),
            'comorbidities' => $this->getRandomComorbidities(),
            'last_blood_test_date' => Carbon::now()->subMonths(rand(1, 12)),
            'urine_protein_creatinine_ratio' => rand(1, 100) <= 10 ? rand(1, 300) / 10 : null, // 10% chance of elevated
        ];

        ClinicalData::create($clinicalData);
    }

    /**
     * Get realistic weight based on age and height
     */
    private function getRealisticWeight(int $age, int $heightCm): float
    {
        $baseWeight = ($heightCm - 100) + rand(-10, 15);
        
        // Adjust for age (older patients tend to be heavier)
        if ($age > 50) {
            $baseWeight += rand(0, 10);
        }
        
        return round($baseWeight + (rand(-50, 50) / 10), 1);
    }

    /**
     * Get random ethnicity code
     */
    private function getRandomEthnicityCode(): string
    {
        $ethnicityCodes = [
            'A' => 'English, Welsh, Scottish, Northern Irish or British',
            'B' => 'Irish',
            'C' => 'Gypsy or Irish Traveller',
            'D' => 'Roma',
            'E' => 'Any other White background',
            'F' => 'White and Black Caribbean',
            'G' => 'White and Black African',
            'H' => 'White and Asian',
            'I' => 'Any other Mixed or Multiple ethnic background',
            'J' => 'Indian',
            'K' => 'Pakistani',
            'L' => 'Bangladeshi',
            'M' => 'Chinese',
            'N' => 'Any other Asian background',
            'O' => 'African',
            'P' => 'Caribbean',
            'R' => 'Any other Black, Black British, or Caribbean background',
            'S' => 'Arab',
            'Z' => 'Any other ethnic group',
        ];
        
        $codes = array_keys($ethnicityCodes);
        return $codes[array_rand($codes)];
    }

    /**
     * Get random ethnicity description
     */
    private function getRandomEthnicityDescription(): string
    {
        $ethnicityCodes = [
            'A' => 'English, Welsh, Scottish, Northern Irish or British',
            'B' => 'Irish',
            'C' => 'Gypsy or Irish Traveller',
            'D' => 'Roma',
            'E' => 'Any other White background',
            'F' => 'White and Black Caribbean',
            'G' => 'White and Black African',
            'H' => 'White and Asian',
            'I' => 'Any other Mixed or Multiple ethnic background',
            'J' => 'Indian',
            'K' => 'Pakistani',
            'L' => 'Bangladeshi',
            'M' => 'Chinese',
            'N' => 'Any other Asian background',
            'O' => 'African',
            'P' => 'Caribbean',
            'R' => 'Any other Black, Black British, or Caribbean background',
            'S' => 'Arab',
            'Z' => 'Any other ethnic group',
        ];
        
        $codes = array_keys($ethnicityCodes);
        $selectedCode = $codes[array_rand($codes)];
        return $ethnicityCodes[$selectedCode];
    }

    /**
     * Get random smoking status
     */
    private function getRandomSmokingStatus(): string
    {
        $statuses = [
            'never_smoked' => 60, // 60% never smoked
            'current_smoker' => 15, // 15% current smokers
            'ex_smoker' => 20, // 20% ex-smokers
            'vaping' => 3, // 3% vaping
            'occasional_smoker' => 2, // 2% occasional
        ];
        
        $rand = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($rand <= $cumulative) {
                return $status;
            }
        }
        
        return 'never_smoked';
    }

    /**
     * Get random medications
     */
    private function getRandomMedications(): ?array
    {
        $medications = [
            [
                'bnf_code' => '0205050A0AA',
                'dose' => '5mg',
                'frequency' => 'once_daily'
            ],
            [
                'bnf_code' => '0205050A0AB',
                'dose' => '10mg',
                'frequency' => 'once_daily'
            ],
            [
                'bnf_code' => '0601010A0AA',
                'dose' => '500mg',
                'frequency' => 'twice_daily'
            ],
            [
                'bnf_code' => '0212000A0AA',
                'dose' => '20mg',
                'frequency' => 'once_daily'
            ],
            [
                'bnf_code' => '0209000A0AA',
                'dose' => '75mg',
                'frequency' => 'once_daily'
            ],
        ];
        
        // 30% chance of having medications
        if (rand(1, 100) <= 30) {
            $count = rand(1, 3);
            $selectedKeys = array_rand($medications, min($count, count($medications)));
            if (!is_array($selectedKeys)) {
                $selectedKeys = [$selectedKeys];
            }
            return array_values(array_intersect_key($medications, array_flip($selectedKeys)));
        }
        
        return null;
    }

    /**
     * Get random comorbidities
     */
    private function getRandomComorbidities(): ?array
    {
        $comorbidities = [
            'stroke',
            'diabetes_type_1',
            'diabetes_type_2',
            'atrial_fibrillation',
            'transient_ischaemic_attack',
            'chronic_kidney_disease',
            'others',
        ];
        
        // 40% chance of having comorbidities
        if (rand(1, 100) <= 40) {
            $count = rand(1, 3);
            $selectedKeys = array_rand($comorbidities, min($count, count($comorbidities)));
            if (!is_array($selectedKeys)) {
                $selectedKeys = [$selectedKeys];
            }
            return array_values(array_intersect_key($comorbidities, array_flip($selectedKeys)));
        }
        
        return null;
    }
}
