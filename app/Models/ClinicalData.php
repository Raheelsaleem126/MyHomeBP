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
        'height',
        'weight',
        'bmi',
        'ethnicity_code',
        'ethnicity_description',
        'smoking_status',
        'last_blood_test_date',
        'urine_protein_creatinine_ratio',
        'comorbidities',
        'hypertension_diagnosis',
        'medications',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'bmi' => 'decimal:1',
            'last_blood_test_date' => 'date',
            'urine_protein_creatinine_ratio' => 'decimal:2',
            'comorbidities' => 'array',
            'medications' => 'array',
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
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 1);
        }
        
        return null;
    }

    /**
     * Auto-calculate BMI when height or weight is updated.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->height && $model->weight) {
                $model->bmi = $model->calculateBmi();
            }
        });
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
            'others' => 'Others',
        ];
    }

    /**
     * Get hypertension diagnosis options.
     */
    public static function getHypertensionDiagnosisOptions(): array
    {
        return [
            'yes' => 'Yes',
            'no' => 'No',
            'dont_know' => 'Don\'t Know',
        ];
    }

    /**
     * Get ethnicity code relationship.
     */
    public function ethnicityCode(): BelongsTo
    {
        return $this->belongsTo(EthnicityCode::class, 'ethnicity_code', 'code');
    }

    /**
     * Get formatted medications.
     */
    public function getFormattedMedicationsAttribute(): array
    {
        if (!$this->medications) {
            return [];
        }

        $formatted = [];
        foreach ($this->medications as $medication) {
            $formatted[] = [
                'bnf_code' => $medication['bnf_code'] ?? null,
                'generic_name' => $medication['generic_name'] ?? null,
                'brand_name' => $medication['brand_name'] ?? null,
                'dose' => $medication['dose'] ?? null,
                'frequency' => $medication['frequency'] ?? null,
                'form' => $medication['form'] ?? null,
            ];
        }

        return $formatted;
    }

    /**
     * Get formatted comorbidities.
     */
    public function getFormattedComorbiditiesAttribute(): array
    {
        if (!$this->comorbidities) {
            return [];
        }

        $formatted = [];
        $options = self::getComorbidityOptions();
        
        foreach ($this->comorbidities as $comorbidity) {
            $formatted[] = [
                'code' => $comorbidity,
                'name' => $options[$comorbidity] ?? $comorbidity,
                'description' => null
            ];
        }

        return $formatted;
    }
}