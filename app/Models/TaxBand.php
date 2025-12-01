<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class TaxBand extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    protected $fillable = [
        'organization_id',
        'branch_id',
        'name',
        'min_income',
        'max_income',
        'tax_rate',
        'fixed_amount',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate tax for a given income using progressive tax bands
     */
    public static function calculatePAYE(float $grossSalary): float
    {
        $taxBands = static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($taxBands->isEmpty()) {
            return 0;
        }

        $totalTax = 0;
        $remainingIncome = $grossSalary;

        foreach ($taxBands as $band) {
            if ($remainingIncome <= 0 || $grossSalary < $band->min_income) {
                continue;
            }

            // Calculate taxable amount for this band
            $bandMax = $band->max_income ?? PHP_FLOAT_MAX;
            $bandMin = $band->min_income;
            
            if ($grossSalary <= $bandMin) {
                continue;
            }

            // Amount taxable in this band
            $taxableInBand = min($remainingIncome, min($bandMax, $grossSalary) - $bandMin);
            
            if ($taxableInBand > 0) {
                // Calculate tax for this band
                $bandTax = $taxableInBand * ($band->tax_rate / 100);
                $totalTax += $bandTax;
                $remainingIncome -= $taxableInBand;
            }
        }

        return round($totalTax, 2);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('org', function (Builder $query) {
            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->orWhere('organization_id', "=", NULL);
            }
        });
    }
}

