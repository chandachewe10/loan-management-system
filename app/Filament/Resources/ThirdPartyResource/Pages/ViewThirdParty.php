<?php

namespace App\Filament\Resources\ThirdPartyResource\Pages;

use App\Filament\Resources\ThirdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewThirdParty extends ViewRecord
{
    protected static string $resource = ThirdPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
