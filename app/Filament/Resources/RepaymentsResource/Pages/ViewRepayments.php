<?php

namespace App\Filament\Resources\RepaymentsResource\Pages;

use App\Filament\Resources\RepaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRepayments extends ViewRecord
{
    protected static string $resource = RepaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
