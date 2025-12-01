<?php

namespace App\Filament\Resources\PayrollRunResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PayslipsRelationManager extends RelationManager
{
    protected static string $relationship = 'payslips';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payslip_number')
            ->columns([
                Tables\Columns\TextColumn::make('payslip_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label('Employee #')
                    ->searchable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->label('Gross Salary')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_deductions')
                    ->label('Deductions')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_pay')
                    ->label('Net Pay')
                    ->money('ZMW')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('payslip_sent')
                    ->label('Sent')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => route('payslip.download', ['payslip' => $record->id])),
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => \App\Filament\Resources\PayslipResource::getUrl('view', ['record' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}

