<?php

namespace App\Filament\Resources\SwitchBranchResource\Pages;

use App\Filament\Resources\SwitchBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSwitchBranch extends EditRecord
{
    protected static string $resource = SwitchBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
           // Actions\DeleteAction::make(),
        ];
    }
}
