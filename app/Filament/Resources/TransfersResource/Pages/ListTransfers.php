<?php

namespace App\Filament\Resources\TransfersResource\Pages;

use App\Filament\Resources\TransfersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransfersResource::class;
    protected ?string $heading = 'Funds Transfer';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Transfer'),
        ];
    }
}
