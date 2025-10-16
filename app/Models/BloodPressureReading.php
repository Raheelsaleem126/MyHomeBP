<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BloodPressureReading extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'reading_date',
        'session_type',
        'reading_1_systolic',
        'reading_1_diastolic',
        'reading_1_pulse',
        'reading_2_systolic',
        'reading_2_diastolic',
        'reading_2_pulse',
        'reading_3_systolic',
        'reading_3_diastolic',
        'reading_3_pulse',
        'average_systolic',
        'average_diastolic',
        'average_pulse',
        'reading_category',
        'is_high_reading',
        'requires_urgent_advice',
        'system_response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reading_date' => 'datetime',
            'reading_1_systolic' => 'integer',
            'reading_1_diastolic' => 'integer',
            'reading_1_pulse' => 'integer',
            'reading_2_systolic' => 'integer',
            'reading_2_diastolic' => 'integer',
            'reading_2_pulse' => 'integer',
            'reading_3_systolic' => 'integer',
            'reading_3_diastolic' => 'integer',
            'reading_3_pulse' => 'integer',
            'average_systolic' => 'integer',
            'average_diastolic' => 'integer',
            'average_pulse' => 'integer',
            'is_high_reading' => 'boolean',
            'requires_urgent_advice' => 'boolean',
        ];
    }

    /**
     * Get the patient that owns the blood pressure reading.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the average systolic reading.
     */
    public function getAverageSystolicAttribute(): int
    {
        $readings = array_filter([
            $this->reading_1_systolic,
            $this->reading_2_systolic,
            $this->reading_3_systolic
        ]);
        
        return $readings ? round(array_sum($readings) / count($readings)) : 0;
    }

    /**
     * Get the average diastolic reading.
     */
    public function getAverageDiastolicAttribute(): int
    {
        $readings = array_filter([
            $this->reading_1_diastolic,
            $this->reading_2_diastolic,
            $this->reading_3_diastolic
        ]);
        
        return $readings ? round(array_sum($readings) / count($readings)) : 0;
    }

    /**
     * Get the average pulse reading.
     */
    public function getAveragePulseAttribute(): int
    {
        $readings = array_filter([
            $this->reading_1_pulse,
            $this->reading_2_pulse,
            $this->reading_3_pulse
        ]);
        
        return $readings ? round(array_sum($readings) / count($readings)) : 0;
    }

    /**
     * Check if the reading is high according to NICE guidelines.
     */
    public function isHighReading(): bool
    {
        return $this->average_systolic >= 180 || $this->average_diastolic >= 110;
    }

    /**
     * Check if the reading requires urgent advice.
     */
    public function requiresUrgentAdvice(): bool
    {
        return $this->average_systolic >= 180 && $this->average_diastolic >= 110;
    }

    /**
     * Get the reading category based on NICE guidelines.
     */
    public function getReadingCategoryAttribute(): string
    {
        $systolic = $this->average_systolic;
        $diastolic = $this->average_diastolic;

        if ($systolic >= 180 || $diastolic >= 110) {
            return 'Hypertensive Crisis';
        } elseif ($systolic >= 160 || $diastolic >= 100) {
            return 'Stage 2 Hypertension';
        } elseif ($systolic >= 140 || $diastolic >= 90) {
            return 'Stage 1 Hypertension';
        } elseif ($systolic >= 135 || $diastolic >= 85) {
            return 'High Normal';
        } elseif ($systolic >= 120 || $diastolic >= 80) {
            return 'Normal';
        } else {
            return 'Optimal';
        }
    }

    /**
     * Scope a query to only include readings from the last N days.
     */
    public function scopeFromLastDays($query, int $days)
    {
        return $query->where('reading_date', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include AM readings.
     */
    public function scopeAm($query)
    {
        return $query->where('session_type', 'am');
    }

    /**
     * Scope a query to only include PM readings.
     */
    public function scopePm($query)
    {
        return $query->where('session_type', 'pm');
    }
}