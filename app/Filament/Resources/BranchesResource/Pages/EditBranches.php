<?php

namespace App\Filament\Resources\BranchesResource\Pages;

use App\Filament\Resources\BranchesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranches extends EditRecord
{
    protected static string $resource = BranchesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
