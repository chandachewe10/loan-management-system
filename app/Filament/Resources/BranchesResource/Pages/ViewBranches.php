<?php

namespace App\Filament\Resources\BranchesResource\Pages;

use App\Filament\Resources\BranchesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBranches extends ViewRecord
{
    protected static string $resource = BranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
