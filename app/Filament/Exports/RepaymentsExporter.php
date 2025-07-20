<?php

namespace App\Filament\Exports;

use App\Models\Repayments;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RepaymentsExporter extends Exporter
{
    protected static ?string $model = Repayments::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('loan_number'),
            ExportColumn::make('balance'),
            ExportColumn::make('payments'),
            ExportColumn::make('principal'),
            ExportColumn::make('payments_method'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('reference_number'),
            ExportColumn::make('loan_number'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your repayments export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
