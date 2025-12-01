<?php

namespace App\Filament\Resources\PayrollRunResource\Pages;

use App\Filament\Resources\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payroll Information')
                    ->schema([
                        TextEntry::make('payroll_number'),
                        TextEntry::make('period_name'),
                        TextEntry::make('pay_period_start')
                            ->date(),
                        TextEntry::make('pay_period_end')
                            ->date(),
                        TextEntry::make('payment_date')
                            ->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'processing' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Payslips')
                    ->schema([
                        RepeatableEntry::make('payslips')
                            ->schema([
                                TextEntry::make('employee.full_name')
                                    ->label('Employee'),
                                TextEntry::make('gross_salary')
                                    ->money('ZMW'),
                                TextEntry::make('total_deductions')
                                    ->money('ZMW'),
                                TextEntry::make('net_pay')
                                    ->money('ZMW'),
                                TextEntry::make('payslip_sent')
                                    ->label('Sent')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                                InfolistActions::make([
                                    Action::make('download')
                                        ->label('Download')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->color('success')
                                        ->url(fn ($record) => route('payslip.download', ['payslip' => $record['id']])),
                                ]),
                            ])
                            ->columns(5),
                    ]),
            ]);
    }
}

