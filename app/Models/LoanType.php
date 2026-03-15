<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class LoanType extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }

    protected $fillable = [
        'loan_name',
        'interest_rate',
        'interest_cycle',
        'service_fee_type',
        'service_fee_percentage',
        'service_fee_custom_amount',
        'penalty_fee_type',
        'penalty_fee_percentage',
        'penalty_fee_custom_amount',
        'early_repayment_percent',
        'service_fee',
        'organization_id',
        'branch_id',
    ];

    public function loan()
    {
    return $this->hasMany(Loan::class, 'id','loan_type_id');
    }

    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                 ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }
}
