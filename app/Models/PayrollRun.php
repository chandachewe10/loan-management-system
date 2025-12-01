<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class PayrollRun extends Model
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
        'payroll_number',
        'period_name',
        'pay_period_start',
        'pay_period_end',
        'payment_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
    ];

    public function payslips()
    {
        return $this->hasMany(Payslip::class, 'payroll_run_id', 'id');
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, Payslip::class, 'payroll_run_id', 'id', 'id', 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getTotalGrossSalaryAttribute()
    {
        return $this->payslips()->sum('gross_salary');
    }

    public function getTotalNetPayAttribute()
    {
        return $this->payslips()->sum('net_pay');
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->payslips()->sum('total_deductions');
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

