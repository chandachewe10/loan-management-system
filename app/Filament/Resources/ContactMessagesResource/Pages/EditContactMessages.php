<?php

namespace App\Filament\Resources\ContactMessagesResource\Pages;

use App\Filament\Resources\ContactMessagesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactMessages extends EditRecord
{
    protected static string $resource = ContactMessagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
