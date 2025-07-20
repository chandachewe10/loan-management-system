<?php

namespace App\Filament\Resources\BulkRepaymentsResource\Pages;

use App\Filament\Resources\BulkRepaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkRepayments extends ListRecords
{
    protected static string $resource = BulkRepaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
