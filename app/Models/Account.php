<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected $fillable = [
        'code',
        'name',
        'type',
        'normal_balance',
        'description',
        'is_active',
        'is_system',
        'parent_id',
        'organization_id',
        'branch_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // Account type labels
    public const TYPES = [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Get the running balance for this account.
     * For assets/expenses: debits increase, credits decrease.
     * For liabilities/equity/revenue: credits increase, debits decrease.
     */
    public function getBalance(): float
    {
        $debits = $this->journalEntryLines()->where('type', 'debit')->sum('amount');
        $credits = $this->journalEntryLines()->where('type', 'credit')->sum('amount');

        if ($this->normal_balance === 'debit') {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
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
