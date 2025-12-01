<?php

namespace App\Filament\Resources\SalaryScaleResource\Pages;

use App\Filament\Resources\SalaryScaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSalaryScales extends ManageRecords
{
    protected static string $resource = SalaryScaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

