<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gmc_number',
        'date_of_birth',
        'gender',
        'qualifications',
        'years_of_experience',
        'bio',
        'profile_image',
        'is_active',
        'is_available',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get the clinics that the doctor belongs to.
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_doctor')
                    ->withPivot(['start_date', 'end_date', 'status', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Get the specialities that the doctor has.
     */
    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'doctor_speciality')
                    ->withPivot(['certification_date', 'certification_body', 'notes', 'is_primary'])
                    ->withTimestamps();
    }

    /**
     * Get the patients for the doctor.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get the full name of the doctor.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the primary speciality.
     */
    public function getPrimarySpecialityAttribute()
    {
        return $this->specialities()->wherePivot('is_primary', true)->first();
    }

    /**
     * Scope a query to only include active doctors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include available doctors.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to find doctors by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where(function ($q) use ($name) {
            $q->where('first_name', 'LIKE', '%' . $name . '%')
              ->orWhere('last_name', 'LIKE', '%' . $name . '%');
        });
    }

    /**
     * Scope a query to find doctors by speciality.
     */
    public function scopeBySpeciality($query, int $specialityId)
    {
        return $query->whereHas('specialities', function ($q) use ($specialityId) {
            $q->where('speciality_id', $specialityId);
        });
    }

    /**
     * Scope a query to find doctors by clinic.
     */
    public function scopeByClinic($query, int $clinicId)
    {
        return $query->whereHas('clinics', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId)
              ->where('status', 'active');
        });
    }
}