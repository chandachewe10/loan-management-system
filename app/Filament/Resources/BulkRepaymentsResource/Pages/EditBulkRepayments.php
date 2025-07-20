<?php

namespace App\Filament\Resources\BulkRepaymentsResource\Pages;

use App\Filament\Resources\BulkRepaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkRepayments extends EditRecord
{
    protected static string $resource = BulkRepaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
