<?php

namespace App\Filament\Resources\MessagesResource\Pages;

use App\Filament\Resources\MessagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMessages extends ViewRecord
{
    protected static string $resource = MessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
