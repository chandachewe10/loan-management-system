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

    public function repayments()
    {
        return $this->hasMany(Repayments::class, 'loan_id', 'id');
    }

    /**
     * Calculate EMI (Equated Monthly Installment)
     * Since the system uses simple interest, we calculate equal monthly payments
     * Formula: EMI = Total Repayment Amount / Number of Months
     * Where Total Repayment = Principal + Interest (calculated as simple interest)
     */
    public function calculateEMI(): float
    {
        $principal = (float) $this->principal_amount;
        $interestRate = (float) ($this->interest_rate ?? 0);
        $duration = (float) $this->loan_duration;
        
        // Get total repayment amount (principal + interest)
        $totalRepayment = (float) ($this->repayment_amount ?? 0);
        
        // If repayment_amount is not set, calculate it using simple interest
        if ($totalRepayment == 0) {
            // Simple interest calculation: I = P * R * T
            $interestAmount = ($principal * ($interestRate / 100) * $duration);
            $totalRepayment = $principal + $interestAmount;
        }
        
        // Convert duration to months based on duration_period
        $months = $duration;
        if ($this->duration_period === 'year(s)') {
            $months = $duration * 12;
        } elseif ($this->duration_period === 'week(s)') {
            $months = ceil($duration / 4); // Approximate: 4 weeks = 1 month
        } elseif ($this->duration_period === 'day(s)') {
            $months = ceil($duration / 30); // Approximate: 30 days = 1 month
        }
        // If duration_period is 'month(s)', use duration as is

        if ($months == 0) {
            return $totalRepayment;
        }

        // Equal monthly installment
        $emi = $totalRepayment / $months;
        
        return round($emi, 2);
    }

    /**
     * Generate EMI schedule for the loan
     * Uses simple interest calculation matching the system's approach
     */
    public function generateEMISchedule(): array
    {
        $schedule = [];
        $principal = (float) $this->principal_amount;
        $totalRepayment = (float) ($this->repayment_amount ?? 0);
        $interestRate = (float) ($this->interest_rate ?? 0);
        $duration = (float) $this->loan_duration;
        $startDate = \Carbon\Carbon::parse($this->loan_release_date);
        
        // If repayment_amount is not set, calculate it
        if ($totalRepayment == 0) {
            $interestAmount = ($principal * ($interestRate / 100) * $duration);
            $totalRepayment = $principal + $interestAmount;
        }
        
        // Convert duration to months based on duration_period
        $months = $duration;
        if ($this->duration_period === 'year(s)') {
            $months = $duration * 12;
        } elseif ($this->duration_period === 'week(s)') {
            $months = ceil($duration / 4); // Approximate: 4 weeks = 1 month
        } elseif ($this->duration_period === 'day(s)') {
            $months = ceil($duration / 30); // Approximate: 30 days = 1 month
        }

        $emi = $this->calculateEMI();
        $totalInterest = $totalRepayment - $principal;
        
        // Calculate per-installment amounts (equal distribution)
        $principalPerInstallment = $principal / $months;
        $interestPerInstallment = $totalInterest / $months;

        $outstandingPrincipal = $principal;
        $totalPaid = 0;
        $paidRepayments = $this->repayments()->sum('payments');

        for ($i = 1; $i <= $months; $i++) {
            $paymentDate = $startDate->copy()->addMonths($i - 1);
            
            // For the last installment, adjust to ensure exact totals
            if ($i == $months) {
                $principalComponent = $outstandingPrincipal;
                $interestComponent = $totalRepayment - ($totalPaid + $principalComponent);
                $emi = $principalComponent + $interestComponent;
            } else {
                $principalComponent = $principalPerInstallment;
                $interestComponent = $interestPerInstallment;
            }
            
            $outstandingPrincipal -= $principalComponent;
            if ($outstandingPrincipal < 0) {
                $outstandingPrincipal = 0;
            }
            
            $totalPaid += $emi;
            
            // Check if this payment has been made based on cumulative payments
            $isPaid = $paidRepayments >= ($emi * $i);
            $paidAmount = $isPaid ? $emi : 0;
            $remainingBalance = $outstandingPrincipal;

            $schedule[] = [
                'installment_number' => $i,
                'payment_date' => $paymentDate->format('Y-m-d'),
                'payment_date_formatted' => $paymentDate->format('M d, Y'),
                'emi_amount' => round($emi, 2),
                'principal_component' => round($principalComponent, 2),
                'interest_component' => round($interestComponent, 2),
                'outstanding_principal' => round($outstandingPrincipal, 2),
                'is_paid' => $isPaid,
                'paid_amount' => round($paidAmount, 2),
                'remaining_balance' => round($remainingBalance, 2),
                'is_overdue' => !$isPaid && $paymentDate->isPast(),
            ];
        }

        return $schedule;
    }

    /**
     * Get total EMI amount
     */
    public function getTotalEMIAmount(): float
    {
        $schedule = $this->generateEMISchedule();
        return collect($schedule)->sum('emi_amount');
    }

    /**
     * Get remaining installments
     */
    public function getRemainingInstallments(): int
    {
        $schedule = $this->generateEMISchedule();
        return collect($schedule)->where('is_paid', false)->count();
    }

    /**
     * Get paid installments
     */
    public function getPaidInstallments(): int
    {
        $schedule = $this->generateEMISchedule();
        return collect($schedule)->where('is_paid', true)->count();
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
        'organization_id',
        'branch_id',
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
