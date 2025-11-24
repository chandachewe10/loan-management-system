<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Loan extends Model implements HasMedia
{
    use HasFactory;
    use LogsActivity;
    use InteractsWithMedia;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
    public function loan_type()
    {

        return $this->belongsTo(LoanType::class, 'loan_type_id', 'id');
    }

    public function borrower()
    {

        return $this->belongsTo(Borrower::class, 'borrower_id', 'id');
    }



    protected $casts = [
        'activate_loan_agreement_form' => 'boolean',
        'ai_scored_at' => 'datetime',
        'risk_factors' => 'array',
        'recurring_allowances' => 'array',
        'other_allowances' => 'array',
        'other_statutory_deductions' => 'array',
        'other_recurring_deductions' => 'array',
        'basic_pay' => 'decimal:2',
        'total_recurring_allowances' => 'decimal:2',
        'paye' => 'decimal:2',
        'pension_napsa' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'calculated_net_pay' => 'decimal:2',
        'actual_net_pay_payslip' => 'decimal:2',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balance',
        'loan_status',
        'basic_pay',
        'recurring_allowances',
        'total_recurring_allowances',
        'other_allowances',
        'paye',
        'pension_napsa',
        'health_insurance',
        'other_statutory_deductions',
        'other_recurring_deductions',
        'calculated_net_pay',
        'actual_net_pay_payslip',
        'qualification_status',
        'qualification_notes',
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
}
