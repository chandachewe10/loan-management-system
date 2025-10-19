<?php

namespace App\Filament\Exports;

use App\Models\AssetCategory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AssetCategoryExporter extends Exporter
{
    protected static ?string $model = AssetCategory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('code'),
            ExportColumn::make('description'),
            ExportColumn::make('default_depreciation_rate'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('organization_id'),
            ExportColumn::make('branch_id'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset category export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
