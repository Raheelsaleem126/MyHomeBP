<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comorbidity;

class ComorbiditySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comorbidities = [
            [
                'code' => 'stroke',
                'name' => 'Stroke',
                'description' => 'A medical condition in which poor blood flow to the brain results in cell death',
                'sort_order' => 1,
            ],
            [
                'code' => 'diabetes_type_1',
                'name' => 'Diabetes Mellitus (Type 1)',
                'description' => 'A chronic condition in which the pancreas produces little or no insulin',
                'sort_order' => 2,
            ],
            [
                'code' => 'diabetes_type_2',
                'name' => 'Diabetes Mellitus (Type 2)',
                'description' => 'A chronic condition that affects the way the body processes blood sugar (glucose)',
                'sort_order' => 3,
            ],
            [
                'code' => 'atrial_fibrillation',
                'name' => 'Atrial Fibrillation',
                'description' => 'An irregular and often very rapid heart rhythm that can lead to blood clots in the heart',
                'sort_order' => 4,
            ],
            [
                'code' => 'transient_ischaemic_attack',
                'name' => 'Transient Ischaemic Attack',
                'description' => 'A temporary period of symptoms similar to those of a stroke, usually lasting only a few minutes',
                'sort_order' => 5,
            ],
            [
                'code' => 'chronic_kidney_disease',
                'name' => 'Chronic Kidney Disease',
                'description' => 'A long-term condition where the kidneys do not work as well as they should',
                'sort_order' => 6,
            ],
        ];

        foreach ($comorbidities as $comorbidity) {
            Comorbidity::create($comorbidity);
        }
    }
}
