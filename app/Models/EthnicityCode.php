<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EthnicityCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'description',
        'category',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the clinical data records that use this ethnicity code.
     */
    public function clinicalData(): HasMany
    {
        return $this->hasMany(ClinicalData::class, 'ethnicity_code', 'code');
    }

    /**
     * Scope a query to only include active ethnicity codes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to find ethnicity codes by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all active ethnicity codes grouped by category.
     */
    public static function getGroupedByCategory(): array
    {
        return self::active()
            ->orderBy('category')
            ->orderBy('description')
            ->get()
            ->groupBy('category')
            ->toArray();
    }
}