<?php

namespace App\Filament\Exports;

use App\Models\Branches;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BranchesExporter extends Exporter
{
    protected static ?string $model = Branches::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('branch_name'),
            ExportColumn::make('address'),
            ExportColumn::make('street'),
            ExportColumn::make('mobile'),
            ExportColumn::make('email'),
            ExportColumn::make('city'),
            ExportColumn::make('province'),
            ExportColumn::make('user.name')
                ->label('Branch Manager'),
            ExportColumn::make('zipcode'),
            ExportColumn::make('added_by'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('organization_id'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your branches export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
