<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollRunResource\Pages;
use App\Filament\Resources\PayrollRunResource\RelationManagers;
use App\Models\PayrollRun;
use App\Models\Employee;
use App\Models\TaxBand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Payroll Runs';
    protected static ?string $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payroll Period')
                    ->schema([
                        Forms\Components\TextInput::make('period_name')
                            ->label('Period Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-calendar')
                            ->helperText('e.g., "January 2025", "Q1 2025"'),

                        Forms\Components\DatePicker::make('pay_period_start')
                            ->label('Pay Period Start')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar-days'),

                        Forms\Components\DatePicker::make('pay_period_end')
                            ->label('Pay Period End')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar-days'),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-banknotes'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Select Employees')
                    ->description('Choose employees to include in this payroll run')
                    ->schema([
                        Forms\Components\CheckboxList::make('employee_ids')
                            ->label('Employees')
                            ->options(fn () => Employee::where('is_active', true)
                                ->get()
                                ->mapWithKeys(fn ($employee) => [
                                    $employee->id => $employee->employee_number . ' - ' . $employee->full_name
                                ])
                                ->toArray())
                            ->columns(2)
                            ->searchable()
                            ->required()
                            ->helperText('Select all employees to include in this payroll run'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll_number')
                    ->label('Payroll #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pay_period_start')
                    ->label('Period Start')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pay_period_end')
                    ->label('Period End')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payslips_count')
                    ->label('Employees')
                    ->counts('payslips')
                    ->badge(),

                Tables\Columns\TextColumn::make('total_net_pay')
                    ->label('Total Net Pay')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->label('Process Payroll')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record) {
                        if ($record->status !== 'draft') {
                            Notification::make()
                                ->title('Error')
                                ->body('Only draft payroll runs can be processed')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update(['status' => 'processing']);
                        
                        // Get employees from existing payslips BEFORE deleting them
                        $employeeIds = $record->payslips()->pluck('employee_id')->toArray();
                        
                        if (empty($employeeIds)) {
                            Notification::make()
                                ->title('Error')
                                ->body('No employees selected for this payroll run. Please edit the payroll run and select employees.')
                                ->danger()
                                ->send();
                            $record->update(['status' => 'draft']);
                            return;
                        }
                        
                        // Delete existing placeholder payslips
                        $record->payslips()->delete();
                        
                        // Get employees and create payslips with calculated amounts
                        $employees = Employee::whereIn('id', $employeeIds)->get();
                        
                        foreach ($employees as $employee) {
                            self::createPayslip($record, $employee);
                        }

                        $record->update(['status' => 'completed']);

                        Notification::make()
                            ->title('Payroll Processed')
                            ->body('Payroll has been processed successfully for ' . $employees->count() . ' employee(s)')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PayrollRun $record) => $record->status === 'draft'),

                Tables\Actions\Action::make('send_payslips')
                    ->label('Send Payslips')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record) {
                        if ($record->status !== 'completed') {
                            Notification::make()
                                ->title('Error')
                                ->body('Payroll must be completed before sending payslips')
                                ->danger()
                                ->send();
                            return;
                        }

                        $payslips = $record->payslips()->where('payslip_sent', false)->get();
                        
                        if ($payslips->isEmpty()) {
                            Notification::make()
                                ->title('Info')
                                ->body('All payslips have already been sent')
                                ->info()
                                ->send();
                            return;
                        }

                        $sent = 0;
                        foreach ($payslips as $payslip) {
                            try {
                                \App\Notifications\PayslipNotification::send($payslip);
                                $sent++;
                            } catch (\Exception $e) {
                                \Log::error('Failed to send payslip: ' . $e->getMessage());
                            }
                        }

                        Notification::make()
                            ->title('Payslips Sent')
                            ->body("Successfully sent {$sent} payslip(s) via email")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PayrollRun $record) => $record->status === 'completed'),

                Tables\Actions\Action::make('downloadAllPayslips')
                    ->label('Download All Payslips')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (PayrollRun $record) {
                        // Generate a zip file or redirect to a page with all payslips
                        // For now, we'll just show a notification
                        Notification::make()
                            ->title('Download Payslips')
                            ->body('Please download individual payslips from the payroll run view page.')
                            ->info()
                            ->send();
                    })
                    ->visible(fn (PayrollRun $record) => $record->status === 'completed'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (PayrollRun $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function createPayslip(PayrollRun $payrollRun, Employee $employee): void
    {
        // Calculate gross salary
        $grossSalary = $employee->gross_salary;
        $basicSalary = $employee->basic_salary;

        // Calculate PAYE using tax bands (on gross salary)
        $paye = TaxBand::calculatePAYE($grossSalary);

        // Calculate NAPSA (5% of gross salary)
        $napsa = $grossSalary * 0.05;

        // Calculate NHIMA (1% of basic salary)
        $nhima = $basicSalary * 0.01;

        // Other deductions (can be customized)
        $otherDeductions = 0;

        $totalDeductions = $paye + $napsa + $nhima + $otherDeductions;
        $netPay = $grossSalary - $totalDeductions;

        \App\Models\Payslip::create([
            'payroll_run_id' => $payrollRun->id,
            'employee_id' => $employee->id,
            'basic_salary' => $employee->basic_salary,
            'housing_allowance' => $employee->housing_allowance,
            'transport_allowance' => $employee->transport_allowance,
            'medical_allowance' => $employee->medical_allowance,
            'other_allowances' => $employee->other_allowances,
            'gross_salary' => $grossSalary,
            'paye' => $paye,
            'napsa' => $napsa,
            'nhima' => $nhima,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PayslipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view' => Pages\ViewPayrollRun::route('/{record}'),
            'edit' => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}

