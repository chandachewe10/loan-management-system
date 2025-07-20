<?php

namespace App\Filament\Resources\BulkRepaymentsResource\Pages;

use App\Filament\Resources\BulkRepaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Imports\RepaymentsImporter;
use Filament\Actions\ImportAction;

class CreateBulkRepayments extends CreateRecord
{
    protected static string $resource = BulkRepaymentsResource::class;

     protected function getFormActions(): array
    {
        return [
            ImportAction::make()
            ->importer(RepaymentsImporter::class) 
                ->label('Upload Bulk Repayments')
                ->color('primary') 
                ->icon('heroicon-o-arrow-up-on-square') 
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
