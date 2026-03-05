<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\DoubleEntryService;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     * Automatically post a double-entry journal entry for the expense.
     */
    public function created(Expense $expense): void
    {
        $expense->organization_id = auth()->user()->organization_id;
        $expense->branch_id = auth()->user()->branch_id;
        $expense->save();

        // Post journal entry for the expense
        DoubleEntryService::recordExpense($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
