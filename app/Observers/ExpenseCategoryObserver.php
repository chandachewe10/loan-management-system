<?php

namespace App\Observers;

use App\Models\ExpenseCategory;

class ExpenseCategoryObserver
{
    /**
     * Handle the ExpenseCategory "created" event.
     */
    public function created(ExpenseCategory $expenseCategory): void
    {

            $expenseCategory->organization_id = auth()->user()->organization_id;
            $expenseCategory->branch_id = auth()->user()->branch_id;
            $expenseCategory->save();
    }

    /**
     * Handle the ExpenseCategory "updated" event.
     */
    public function updated(ExpenseCategory $expenseCategory): void
    {



    }

    /**
     * Handle the ExpenseCategory "deleted" event.
     */
    public function deleted(ExpenseCategory $expenseCategory): void
    {
        //
    }

    /**
     * Handle the ExpenseCategory "restored" event.
     */
    public function restored(ExpenseCategory $expenseCategory): void
    {
        //
    }

    /**
     * Handle the ExpenseCategory "force deleted" event.
     */
    public function forceDeleted(ExpenseCategory $expenseCategory): void
    {
        //
    }
}
