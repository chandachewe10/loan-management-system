<?php

namespace App\Filament\Resources\SubscriptionsResource\Pages;

use App\Filament\Resources\SubscriptionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptions extends ViewRecord
{
    protected static string $resource = SubscriptionsResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make(),
    //     ];
    // }
}
