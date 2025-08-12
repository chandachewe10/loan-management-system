<?php

namespace App\Filament\Resources\SubscriptionsResource\Pages;

use App\Filament\Resources\SubscriptionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriptions extends CreateRecord
{
    protected static bool $canCreateAnother = false;
    protected static string $resource = SubscriptionsResource::class;

protected function getFormActions(): array
{
    return [];
}

}
