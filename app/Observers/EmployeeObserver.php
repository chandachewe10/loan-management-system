<?php

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver
{
    public function creating(Employee $employee): void
    {
        if (auth()->check()) {
            $employee->organization_id = auth()->user()->organization_id;
            $employee->branch_id = auth()->user()->branch_id;
        }
    }
}

