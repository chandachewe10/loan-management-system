<?php

namespace App\Filament\Resources\DirectDebitMandateSettingsResource\Pages;

use App\Filament\Resources\DirectDebitMandateSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDirectDebitMandateSettings extends EditRecord
{
    protected static string $resource = DirectDebitMandateSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

