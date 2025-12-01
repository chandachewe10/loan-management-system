<?php

namespace App\Observers;

use App\Models\Payslip;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PayslipObserver
{
    public function creating(Payslip $payslip): void
    {
        if (auth()->check()) {
            $payslip->organization_id = auth()->user()->organization_id;
            $payslip->branch_id = auth()->user()->branch_id;
        }

        if (empty($payslip->payslip_number)) {
            $payslip->payslip_number = IdGenerator::generate([
                'table' => 'payslips',
                'field' => 'payslip_number',
                'length' => 12,
                'prefix' => 'PS-'
            ]);
        }
    }
}

