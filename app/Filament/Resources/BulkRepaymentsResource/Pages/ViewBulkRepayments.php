<?php

namespace App\Filament\Resources\BulkRepaymentsResource\Pages;

use App\Filament\Resources\BulkRepaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBulkRepayments extends ViewRecord
{
    protected static string $resource = BulkRepaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
