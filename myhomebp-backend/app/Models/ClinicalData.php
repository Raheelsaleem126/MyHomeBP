<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'height_cm',
        'height_ft',
        'height_inches',
        'weight_kg',
        'weight_stones',
        'weight_lbs',
        'bmi',
        'ethnicity',
        'smoking_status',
        'hypertension_diagnosis',
        'medications',
        'comorbidities',
        'last_blood_test',
        'urine_protein_creatinine_ratio',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'height_cm' => 'integer',
            'height_ft' => 'integer',
            'height_inches' => 'integer',
            'weight_kg' => 'decimal:2',
            'weight_stones' => 'integer',
            'weight_lbs' => 'decimal:2',
            'bmi' => 'decimal:1',
            'hypertension_diagnosis' => 'boolean',
            'medications' => 'array',
            'comorbidities' => 'array',
            'last_blood_test' => 'date',
            'urine_protein_creatinine_ratio' => 'decimal:2',
        ];
    }

    /**
     * Get the patient that owns the clinical data.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Calculate BMI from height and weight.
     */
    public function calculateBmi(): ?float
    {
        if ($this->height_cm && $this->weight_kg) {
            $heightInMeters = $this->height_cm / 100;
            return round($this->weight_kg / ($heightInMeters * $heightInMeters), 1);
        }
        
        return null;
    }

    /**
     * Get BMI category.
     */
    public function getBmiCategoryAttribute(): string
    {
        if (!$this->bmi) {
            return 'Unknown';
        }

        if ($this->bmi < 18.5) {
            return 'Underweight';
        } elseif ($this->bmi < 25) {
            return 'Normal';
        } elseif ($this->bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    /**
     * Get available smoking status options.
     */
    public static function getSmokingStatusOptions(): array
    {
        return [
            'never_smoked' => 'Never Smoked',
            'current_smoker' => 'Current Smoker',
            'ex_smoker' => 'Ex-smoker',
            'vaping' => 'Vaping',
            'occasional_smoker' => 'Occasional Smoker',
        ];
    }

    /**
     * Get available comorbidities.
     */
    public static function getComorbidityOptions(): array
    {
        return [
            'stroke' => 'Stroke',
            'diabetes_type_1' => 'Diabetes Mellitus (Type 1)',
            'diabetes_type_2' => 'Diabetes Mellitus (Type 2)',
            'atrial_fibrillation' => 'Atrial Fibrillation',
            'transient_ischaemic_attack' => 'Transient Ischaemic Attack',
            'chronic_kidney_disease' => 'Chronic Kidney Disease',
        ];
    }
}