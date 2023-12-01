<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use App\Filament\Resources\TransfersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransfers extends EditRecord
{
    protected static string $resource = TransfersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
