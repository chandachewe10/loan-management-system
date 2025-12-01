<?php

namespace App\Observers;

use App\Models\SalaryScale;

class SalaryScaleObserver
{
    public function creating(SalaryScale $salaryScale): void
    {
        if (auth()->check()) {
            $salaryScale->organization_id = auth()->user()->organization_id;
            $salaryScale->branch_id = auth()->user()->branch_id;
        }
    }
}

