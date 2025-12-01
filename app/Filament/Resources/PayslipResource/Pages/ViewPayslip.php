<?php

namespace App\Filament\Resources\PayslipResource\Pages;

use App\Filament\Resources\PayslipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayslip extends ViewRecord
{
    protected static string $resource = PayslipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Download Payslip')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => route('payslip.download', ['payslip' => $this->record->id])),
        ];
    }
}

