<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'postcode',
        'phone',
        'email',
        'type',
        'latitude',
        'longitude',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the patients for the clinic.
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Get the doctors that belong to this clinic.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'clinic_doctor')
                    ->withPivot(['start_date', 'end_date', 'status', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active clinics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to find clinics by postcode.
     */
    public function scopeByPostcode($query, string $postcode)
    {
        return $query->where('postcode', 'LIKE', '%' . strtoupper($postcode) . '%');
    }

    /**
     * Scope a query to find clinics by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'LIKE', '%' . $name . '%');
    }
}