<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use App\Models\PayrollRun;
use App\Models\Employee;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CreatePayrollRun extends CreateRecord
{
    protected static string $resource = PayrollRunResource::class;

    public ?array $employeeIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store employee_ids before removing from data
        $this->employeeIds = $data['employee_ids'] ?? [];
        // Remove employee_ids from data as it's not a field in payroll_runs table
        unset($data['employee_ids']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->employeeIds)) {
            // Create placeholder payslips for selected employees
            foreach ($this->employeeIds as $employeeId) {
                \App\Models\Payslip::create([
                    'payroll_run_id' => $this->record->id,
                    'employee_id' => $employeeId,
                    'basic_salary' => 0,
                    'gross_salary' => 0,
                    'total_deductions' => 0,
                    'net_pay' => 0,
                ]);
            }
        }
    }
}

