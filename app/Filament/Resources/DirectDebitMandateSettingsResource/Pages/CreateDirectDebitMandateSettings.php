<?php

namespace App\Filament\Resources\DirectDebitMandateSettingsResource\Pages;

use App\Filament\Resources\DirectDebitMandateSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDirectDebitMandateSettings extends CreateRecord
{
    protected static string $resource = DirectDebitMandateSettingsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->check()) {
            $data['organization_id'] = auth()->user()->organization_id;
            $data['branch_id'] = auth()->user()->branch_id;
        }
        
        return $data;
    }
}

