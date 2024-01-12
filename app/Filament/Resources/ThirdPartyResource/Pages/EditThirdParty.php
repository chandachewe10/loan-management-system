<?php

namespace App\Filament\Resources\ThirdPartyResource\Pages;

use App\Filament\Resources\ThirdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThirdParty extends EditRecord
{
    protected static string $resource = ThirdPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
