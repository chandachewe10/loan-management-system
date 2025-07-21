<?php

namespace App\Filament\Resources\ContactMessagesResource\Pages;

use App\Filament\Resources\ContactMessagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessages extends ViewRecord
{
    protected static string $resource = ContactMessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
