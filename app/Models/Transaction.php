<?php

namespace App\Models;

use Bavix\Wallet\Models\Transaction as TransactionBaseWallet;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends TransactionBaseWallet
{
    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }
}
