<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class Payslip extends Model
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
        'payroll_run_id',
        'employee_id',
        'payslip_number',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'medical_allowance',
        'other_allowances',
        'gross_salary',
        'paye',
        'napsa',
        'nhima',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'payslip_sent',
        'payslip_sent_at',
        'payslip_file_path',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'paye' => 'decimal:2',
        'napsa' => 'decimal:2',
        'nhima' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'payslip_sent' => 'boolean',
        'payslip_sent_at' => 'datetime',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
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

