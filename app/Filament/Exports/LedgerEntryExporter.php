<?php

namespace App\Filament\Exports;

use App\Models\LedgerEntry;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LedgerEntryExporter extends Exporter
{
    protected static ?string $model = LedgerEntry::class;

    public static function getColumns(): array
    {
        return [
          
              ExportColumn::make('created_at')
             ->label('Date'),
              ExportColumn::make('account_type.name')
             ->label('Account'),
              ExportColumn::make('debit')
             ->label('Debit'),
               ExportColumn::make('credit')
             ->label('Debit'),
               ExportColumn::make('transaction_type.uuid')
             ->label('Reference'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your ledger entry export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
