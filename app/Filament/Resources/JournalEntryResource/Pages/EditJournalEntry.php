<?php

namespace App\Filament\Resources\JournalEntryResource\Pages;

use App\Filament\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // If it was just changed to posted, sync to wallets
        if ($record->wasChanged('status') && $record->status === 'posted') {
            foreach ($record->lines as $line) {
                $wallet = \App\Models\Wallet::withoutGlobalScopes()->where('account_id', $line->account_id)->first();
                if ($wallet) {
                    $amount = (float) $line->amount;
                    if ($line->type === 'debit') {
                        $wallet->deposit($amount, ['meta' => 'Manual Journal Entry: ' . $record->entry_number, 'journal_entry_id' => $record->id]);
                    } else {
                        $wallet->withdraw($amount, ['meta' => 'Manual Journal Entry: ' . $record->entry_number, 'journal_entry_id' => $record->id]);
                    }
                }
            }
        }
    }
}
