<?php

namespace App\Filament\Resources\TaxBandResource\Pages;

use App\Filament\Resources\TaxBandResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTaxBands extends ManageRecords
{
    protected static string $resource = TaxBandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

