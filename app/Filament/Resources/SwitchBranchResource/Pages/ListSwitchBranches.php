<?php

namespace App\Filament\Resources\SwitchBranchResource\Pages;

use App\Filament\Resources\SwitchBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSwitchBranches extends ListRecords
{
    protected static string $resource = SwitchBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
