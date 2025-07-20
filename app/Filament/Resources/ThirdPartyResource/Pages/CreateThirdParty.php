<?php

namespace App\Filament\Resources\ThirdPartyResource\Pages;

use App\Filament\Resources\ThirdPartyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateThirdParty extends CreateRecord
{
    protected static string $resource = ThirdPartyResource::class;


       protected function getRedirectUrl(): string
    {
       
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Third Party')
            ->body('The Third party has been created successfully.');
    }
}
