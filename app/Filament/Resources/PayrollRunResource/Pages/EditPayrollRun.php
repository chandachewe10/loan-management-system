<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollRun extends EditRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected array $employeeIds = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Populate employee_ids from existing payslips
        $data['employee_ids'] = $this->record->payslips()->pluck('employee_id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store employee_ids for later processing
        $this->employeeIds = $data['employee_ids'] ?? [];
        // Remove employee_ids from data as it's not a field in payroll_runs table
        unset($data['employee_ids']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Only update employees if payroll is still in draft status
        if ($this->record->status === 'draft' && !empty($this->employeeIds)) {
            // Delete existing placeholder payslips
            $this->record->payslips()->delete();
            
            // Create new placeholder payslips for selected employees
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

