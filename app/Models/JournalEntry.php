<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JournalEntry extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'source_type',
        'source_id',
        'source_model',
        'status',
        'reference',
        'created_by',
        'organization_id',
        'branch_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public const SOURCE_TYPES = [
        'loan_disbursement' => 'Loan Disbursement',
        'loan_repayment' => 'Loan Repayment',
        'expense' => 'Expense',
        'manual' => 'Manual',
        'payroll' => 'Payroll',
        'transfer' => 'Transfer',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Validate that the entry is balanced (debits == credits).
     */
    public function isBalanced(): bool
    {
        $totalDebits = $this->lines()->where('type', 'debit')->sum('amount');
        $totalCredits = $this->lines()->where('type', 'credit')->sum('amount');

        return abs($totalDebits - $totalCredits) < 0.01;
    }

    public function getTotalDebitsAttribute(): float
    {
        return (float) $this->lines()->where('type', 'debit')->sum('amount');
    }

    public function getTotalCreditsAttribute(): float
    {
        return (float) $this->lines()->where('type', 'credit')->sum('amount');
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
