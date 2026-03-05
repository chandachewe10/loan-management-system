<?php

namespace App\Observers;

use App\Models\JournalEntry;
use App\Services\DoubleEntryService;

class JournalEntryObserver
{
    /**
     * Handle the JournalEntry "creating" event.
     * Auto-generate entry_number and set org/branch on manual entries.
     */
    public function creating(JournalEntry $entry): void
    {
        if (empty($entry->entry_number)) {
            $entry->entry_number = DoubleEntryService::generateEntryNumber();
        }

        if (auth()->check()) {
            if (!$entry->organization_id) {
                $entry->organization_id = auth()->user()->organization_id;
            }
            if (!$entry->branch_id) {
                $entry->branch_id = auth()->user()->branch_id;
            }
            if (!$entry->created_by) {
                $entry->created_by = auth()->id();
            }
        }
    }

    public function updated(JournalEntry $entry): void
    {
    }
    public function deleted(JournalEntry $entry): void
    {
    }
    public function restored(JournalEntry $entry): void
    {
    }
    public function forceDeleted(JournalEntry $entry): void
    {
    }
}
