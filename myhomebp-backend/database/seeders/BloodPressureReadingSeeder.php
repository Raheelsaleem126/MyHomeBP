<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\BloodPressureReading;
use Carbon\Carbon;

class BloodPressureReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first patient (or create one if none exists)
        $patient = Patient::first();
        
        if (!$patient) {
            $this->command->info('No patients found. Please run the patient registration first.');
            return;
        }

        // Create blood pressure readings for the last 7 days
        $today = Carbon::now();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            
            // AM Reading (morning)
            $this->createReading($patient, $date->copy()->setTime(8, 30), 'am', [
                'systolic' => rand(115, 135),
                'diastolic' => rand(75, 85),
                'pulse' => rand(65, 75)
            ]);
            
            // PM Reading (evening)
            $this->createReading($patient, $date->copy()->setTime(19, 30), 'pm', [
                'systolic' => rand(120, 140),
                'diastolic' => rand(78, 88),
                'pulse' => rand(70, 80)
            ]);
        }

        // Add some additional readings for testing averages
        // Day 8 (should be excluded from 7-day average)
        $day8 = $today->copy()->subDays(8);
        $this->createReading($patient, $day8->copy()->setTime(8, 30), 'am', [
            'systolic' => 150, // High reading
            'diastolic' => 95,
            'pulse' => 80
        ]);

        // Add some readings for other patients if they exist
        $otherPatients = Patient::skip(1)->take(2)->get();
        
        foreach ($otherPatients as $otherPatient) {
            // Create 3 days of readings for other patients
            for ($i = 2; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                
                $this->createReading($otherPatient, $date->copy()->setTime(9, 0), 'am', [
                    'systolic' => rand(110, 130),
                    'diastolic' => rand(70, 80),
                    'pulse' => rand(60, 70)
                ]);
                
                $this->createReading($otherPatient, $date->copy()->setTime(20, 0), 'pm', [
                    'systolic' => rand(115, 135),
                    'diastolic' => rand(75, 85),
                    'pulse' => rand(65, 75)
                ]);
            }
        }

        $this->command->info('Blood pressure readings seeded successfully!');
    }

    private function createReading(Patient $patient, Carbon $date, string $sessionType, array $values)
    {
        // Add some variation to readings
        $reading1 = [
            'systolic' => $values['systolic'] + rand(-3, 3),
            'diastolic' => $values['diastolic'] + rand(-2, 2),
            'pulse' => $values['pulse'] + rand(-2, 2)
        ];

        $reading2 = [
            'systolic' => $values['systolic'] + rand(-2, 2),
            'diastolic' => $values['diastolic'] + rand(-1, 1),
            'pulse' => $values['pulse'] + rand(-1, 1)
        ];

        // Calculate averages
        $avgSystolic = round(($reading1['systolic'] + $reading2['systolic']) / 2);
        $avgDiastolic = round(($reading1['diastolic'] + $reading2['diastolic']) / 2);
        $avgPulse = round(($reading1['pulse'] + $reading2['pulse']) / 2);

        // Check if high reading (≥180/≥110)
        $isHighReading = $avgSystolic >= 180 || $avgDiastolic >= 110;
        $requiresUrgentAdvice = $avgSystolic >= 180 && $avgDiastolic >= 110;

        // Determine system response
        $systemResponse = $this->getSystemResponse($avgSystolic, $avgDiastolic);

        BloodPressureReading::create([
            'patient_id' => $patient->id,
            'reading_date' => $date,
            'session_type' => $sessionType,
            'reading_1_systolic' => $reading1['systolic'],
            'reading_1_diastolic' => $reading1['diastolic'],
            'reading_1_pulse' => $reading1['pulse'],
            'reading_2_systolic' => $reading2['systolic'],
            'reading_2_diastolic' => $reading2['diastolic'],
            'reading_2_pulse' => $reading2['pulse'],
            'reading_3_systolic' => null,
            'reading_3_diastolic' => null,
            'reading_3_pulse' => null,
            'is_high_reading' => $isHighReading,
            'requires_urgent_advice' => $requiresUrgentAdvice,
            'system_response' => $systemResponse,
        ]);
    }

    private function getSystemResponse(int $systolic, int $diastolic): string
    {
        if ($systolic >= 180 || $diastolic >= 110) {
            return "Please wait 5 minutes and recheck (Reading 3).";
        }

        return "Thank you for submitting today's reading.";
    }
}