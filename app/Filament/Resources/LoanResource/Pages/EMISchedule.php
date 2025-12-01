<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Exports\EMIScheduleExporter;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class EMISchedule extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportEMI')
                ->label('Export EMI Schedule')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $schedule = $this->record->generateEMISchedule();
                    $exporter = new EMIScheduleExporter($this->record, $schedule);
                    return Excel::download($exporter, 'emi-schedule-' . $this->record->loan_number . '.xlsx');
                }),
            Actions\Action::make('back')
                ->label('Back to Loan')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => LoanResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    public function getContent(): View
    {
        $schedule = $this->record->generateEMISchedule();
        $totalEMI = $this->record->getTotalEMIAmount();
        $paidInstallments = $this->record->getPaidInstallments();
        $remainingInstallments = $this->record->getRemainingInstallments();
        $monthlyEMI = $this->record->calculateEMI();

        return view('filament.resources.loan-resource.pages.emi-schedule', [
            'record' => $this->record,
            'schedule' => $schedule,
            'totalEMI' => $totalEMI,
            'paidInstallments' => $paidInstallments,
            'remainingInstallments' => $remainingInstallments,
            'monthlyEMI' => $monthlyEMI,
        ]);
    }
}

