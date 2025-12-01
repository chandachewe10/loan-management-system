<?php

namespace App\Observers;

use App\Models\TaxBand;

class TaxBandObserver
{
    public function creating(TaxBand $taxBand): void
    {
        if (auth()->check()) {
            $taxBand->organization_id = auth()->user()->organization_id;
            $taxBand->branch_id = auth()->user()->branch_id;
        }
    }
}

