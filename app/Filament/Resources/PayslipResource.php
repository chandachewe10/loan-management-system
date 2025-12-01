<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayslipResource\Pages;
use App\Models\Payslip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PayslipResource extends Resource
{
    protected static ?string $model = Payslip::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Payslips';
    protected static ?string $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = false; // Hide from navigation, access via PayrollRun

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payroll_run_id')
                    ->relationship('payrollRun', 'period_name')
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('payslip_number')
                    ->required()
                    ->disabled(),

                Forms\Components\Section::make('Earnings')
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->label('Basic Salary')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('housing_allowance')
                            ->label('Housing Allowance')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('transport_allowance')
                            ->label('Transport Allowance')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('medical_allowance')
                            ->label('Medical Allowance')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('other_allowances')
                            ->label('Other Allowances')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('gross_salary')
                            ->label('Gross Salary')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deductions')
                    ->schema([
                        Forms\Components\TextInput::make('paye')
                            ->label('PAYE')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('napsa')
                            ->label('NAPSA')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('nhima')
                            ->label('NHIMA')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('other_deductions')
                            ->label('Other Deductions')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                        Forms\Components\TextInput::make('total_deductions')
                            ->label('Total Deductions')
                            ->prefix('ZMW')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\TextInput::make('net_pay')
                    ->label('Net Pay')
                    ->prefix('ZMW')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payslip_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payrollRun.period_name')
                    ->label('Period')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_pay')
                    ->money('ZMW')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\IconColumn::make('payslip_sent')
                    ->label('Sent')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payroll_run_id')
                    ->relationship('payrollRun', 'period_name')
                    ->label('Payroll Period'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download Payslip')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Payslip $record) {
                        return redirect()->route('payslip.download', ['payslip' => $record->id]);
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayslips::route('/'),
            'view' => Pages\ViewPayslip::route('/{record}'),
        ];
    }
}

