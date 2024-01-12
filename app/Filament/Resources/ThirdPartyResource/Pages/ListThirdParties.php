<?php

namespace App\Filament\Resources\ThirdPartyResource\Pages;

use App\Filament\Resources\ThirdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThirdParties extends ListRecords
{
    protected static string $resource = ThirdPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
