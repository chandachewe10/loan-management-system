<?php

namespace App\Observers;

use App\Models\PayrollRun;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PayrollRunObserver
{
    public function creating(PayrollRun $payrollRun): void
    {
        if (auth()->check()) {
            $payrollRun->organization_id = auth()->user()->organization_id;
            $payrollRun->branch_id = auth()->user()->branch_id;
            $payrollRun->created_by = auth()->user()->id;
        }

        if (empty($payrollRun->payroll_number)) {
            $payrollRun->payroll_number = IdGenerator::generate([
                'table' => 'payroll_runs',
                'field' => 'payroll_number',
                'length' => 10,
                'prefix' => 'PR-'
            ]);
        }
    }
}

