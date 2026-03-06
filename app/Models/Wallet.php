<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet as BaseWallet;
use Illuminate\Database\Eloquent\Builder;

class Wallet extends BaseWallet
{
    /**
     * Link to the Chart of Accounts.
     * Each wallet maps to a sub-account under Cash & Bank (1010).
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the Chart of Accounts code for this wallet.
     * Falls back to the generic "1010" if no linked account exists.
     */
    public function getAccountCode(): string
    {
        return $this->account?->code ?? '1010';
    }

    protected static function booted(): void
    {
        static::addGlobalScope('org', function (Builder $query) {
            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id)
                    ->where('branch_id', auth()->user()->branch_id)
                    ->orWhere('organization_id', '=', null);
            }
        });
    }
}
