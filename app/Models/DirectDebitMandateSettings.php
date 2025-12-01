<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class DirectDebitMandateSettings extends Model
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
        'service_provider_reference_number',
        'days_before_payment_date',
        'days_after_payment_date',
        'default_payment_frequency',
        'payment_date_calculation',
    ];

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

    /**
     * Get settings for current organization/branch or return defaults
     */
    public static function getSettings()
    {
        if (auth()->check()) {
            $settings = static::where('organization_id', auth()->user()->organization_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->first();
            
            if ($settings) {
                return $settings;
            }
        }
        
        // Return default settings
        return new static([
            'service_provider_reference_number' => null,
            'days_before_payment_date' => 5,
            'days_after_payment_date' => 5,
            'default_payment_frequency' => 'M',
            'payment_date_calculation' => 'loan_release_date',
        ]);
    }
}

