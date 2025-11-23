<?php

namespace App\Filament\Resources\BorrowerResource\Pages;

use App\Filament\Resources\BorrowerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBorrower extends ViewRecord
{
    protected static string $resource = BorrowerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('previewApplication')
                ->label('Preview Application')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->url(fn () => route('borrower.application.preview', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
            Actions\Action::make('downloadApplication')
                ->label('Download Application')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => route('borrower.application.download', ['id' => $this->record->id])),
            Actions\EditAction::make(),
        ];
    }
}
