<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    use HasFactory;
    use Notifiable;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    protected $fillable = [
        'organization_id',
        'branch_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_id',
        'position',
        'department',
        'date_of_employment',
        'salary_scale_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'medical_allowance',
        'other_allowances',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'is_active',
    ];

    protected $casts = [
        'date_of_employment' => 'date',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function payrollRuns()
    {
        return $this->belongsToMany(PayrollRun::class, 'payslips', 'employee_id', 'payroll_run_id');
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class, 'employee_id', 'id');
    }

    public function salaryScale()
    {
        return $this->belongsTo(SalaryScale::class, 'salary_scale_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTotalAllowancesAttribute()
    {
        return $this->housing_allowance + $this->transport_allowance + $this->medical_allowance + $this->other_allowances;
    }

    public function getGrossSalaryAttribute()
    {
        return $this->basic_salary + $this->total_allowances;
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

