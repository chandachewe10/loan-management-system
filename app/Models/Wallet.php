<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet as BaseWallet;
use Illuminate\Database\Eloquent\Builder;

class Wallet extends BaseWallet
{
    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }
}
