<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'event_limit',
        'monthly_price',
        'annual_price',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'monthly_price' => 'decimal:2',
            'annual_price' => 'decimal:2',
        ];
    }

    /**
     * Get the teams that belong to this plan.
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Check if this plan has unlimited events.
     */
    public function hasUnlimitedEvents(): bool
    {
        return is_null($this->event_limit);
    }

    /**
     * Get the effective event limit for this plan.
     */
    public function getEventLimit(): int
    {
        return $this->event_limit ?? PHP_INT_MAX;
    }

    /**
     * Calculate annual savings compared to monthly.
     */
    public function getAnnualSavings(): float
    {
        if (!$this->monthly_price || !$this->annual_price) {
            return 0;
        }
        
        $annualAsMonthly = $this->monthly_price * 12;
        return $annualAsMonthly - $this->annual_price;
    }

    /**
     * Get annual savings percentage.
     */
    public function getAnnualSavingsPercentage(): float
    {
        if (!$this->monthly_price || !$this->annual_price) {
            return 0;
        }
        
        $annualAsMonthly = $this->monthly_price * 12;
        return round(($this->getAnnualSavings() / $annualAsMonthly) * 100, 1);
    }
}
