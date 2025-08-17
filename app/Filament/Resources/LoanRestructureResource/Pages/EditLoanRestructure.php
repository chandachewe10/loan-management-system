<?php

namespace App\Filament\Resources\LoanRestructureResource\Pages;

use App\Filament\Resources\LoanRestructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanRestructure extends EditRecord
{
    protected static string $resource = LoanRestructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
