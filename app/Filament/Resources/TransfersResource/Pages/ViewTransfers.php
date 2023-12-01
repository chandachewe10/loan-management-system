<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use App\Filament\Resources\TransfersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransfers extends ViewRecord
{
    protected static string $resource = TransfersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
