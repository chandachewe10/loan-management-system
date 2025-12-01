<?php

namespace App\Filament\Resources\DirectDebitMandateSettingsResource\Pages;

use App\Filament\Resources\DirectDebitMandateSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDirectDebitMandateSettings extends ViewRecord
{
    protected static string $resource = DirectDebitMandateSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

