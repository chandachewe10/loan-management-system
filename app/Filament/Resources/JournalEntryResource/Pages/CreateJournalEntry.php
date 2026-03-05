<?php

namespace App\Filament\Resources\JournalEntryResource\Pages;

use App\Filament\Resources\JournalEntryResource;
use App\Services\DoubleEntryService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entry_number'] = DoubleEntryService::generateEntryNumber();
        $data['source_type'] = $data['source_type'] ?? 'manual';
        $data['created_by'] = auth()->id();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['branch_id'] = auth()->user()->branch_id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Validate that debit == credit
        if (!$record->isBalanced()) {
            Notification::make()
                ->title('Warning: Entry is not balanced!')
                ->body('Total debits (' . number_format($record->total_debits, 2) . ') ≠ Total credits (' . number_format($record->total_credits, 2) . '). Please correct this entry.')
                ->warning()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('Journal Entry posted successfully')
                ->body("Entry {$record->entry_number} — Debits & Credits balanced at ZMW " . number_format($record->total_debits, 2))
                ->success()
                ->send();
        }
    }
}
