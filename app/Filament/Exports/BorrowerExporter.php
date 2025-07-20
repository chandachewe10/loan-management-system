<?php

namespace App\Filament\Exports;

use App\Models\Borrower;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BorrowerExporter extends Exporter
{
    protected static ?string $model = Borrower::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('full_name'),
            ExportColumn::make('gender'),
            ExportColumn::make('dob'),
            ExportColumn::make('occupation'),
            ExportColumn::make('identification'),
            ExportColumn::make('mobile'),
            ExportColumn::make('email'),
            ExportColumn::make('address'),
            ExportColumn::make('city'),
            ExportColumn::make('province'),
            ExportColumn::make('zipcode'),
            ExportColumn::make('created_at'),
            ExportColumn::make('next_of_kin_first_name'),
            ExportColumn::make('next_of_kin_last_name'),
            ExportColumn::make('phone_next_of_kin'),
            ExportColumn::make('address_next_of_kin'),
            ExportColumn::make('relationship_next_of_kin'),
            ExportColumn::make('bank_name'),
            ExportColumn::make('bank_branch'),
            ExportColumn::make('bank_sort_code'),
            ExportColumn::make('bank_account_number'),
            ExportColumn::make('bank_account_name'),
            ExportColumn::make('mobile_money_name'),
            ExportColumn::make('mobile_money_number'),
             ExportColumn::make('created_by.name')
             ->label('Added By'),
           
           
           
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your borrower export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
