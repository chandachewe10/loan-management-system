<?php

namespace App\Filament\Exports;

use App\Models\Asset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetExporter extends Exporter
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('asset_name'),
            ExportColumn::make('asset_code'),
            ExportColumn::make('asset_category.name')
             ->label('Asset Category'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('purchase_cost'),
            ExportColumn::make('supplier'),
            ExportColumn::make('useful_life_years'),
            ExportColumn::make('depreciation_method'),
            ExportColumn::make('depreciation_rate'),
            ExportColumn::make('accumulated_depreciation'),
            ExportColumn::make('net_book_value'),
            ExportColumn::make('location'),
            ExportColumn::make('custodian'),
            ExportColumn::make('status'),
            ExportColumn::make('disposal_date'),
            ExportColumn::make('disposal_value'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('organization_id'),
            ExportColumn::make('branch_id'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
