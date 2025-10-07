<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bnf_code',
        'generic_name',
        'brand_name',
        'form',
        'strength',
        'description',
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
     * Scope a query to only include active medications.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search medications by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('generic_name', 'LIKE', "%{$search}%")
              ->orWhere('brand_name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to find medications by BNF code.
     */
    public function scopeByBnfCode($query, string $bnfCode)
    {
        return $query->where('bnf_code', $bnfCode);
    }

    /**
     * Get the full medication name (generic + brand).
     */
    public function getFullNameAttribute(): string
    {
        if ($this->brand_name && $this->brand_name !== $this->generic_name) {
            return "{$this->generic_name} ({$this->brand_name})";
        }
        
        return $this->generic_name;
    }

    /**
     * Get the medication with strength and form.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->full_name;
        
        if ($this->strength) {
            $name .= " {$this->strength}";
        }
        
        if ($this->form) {
            $name .= " ({$this->form})";
        }
        
        return $name;
    }

    /**
     * Get available dose options for this medication.
     */
    public function getDoseOptionsAttribute(): array
    {
        // This would typically come from a separate table or configuration
        // For now, return common dose options based on the medication
        $commonDoses = [
            '1mg', '2mg', '2.5mg', '5mg', '10mg', '20mg', '25mg', '50mg', '100mg',
            '125mg', '250mg', '500mg', '1g'
        ];
        
        return $commonDoses;
    }

    /**
     * Get available frequency options.
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'once_daily' => 'Once daily',
            'twice_daily' => 'Twice daily',
            'three_times_daily' => 'Three times daily',
            'four_times_daily' => 'Four times daily',
            'as_needed' => 'As needed',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
}