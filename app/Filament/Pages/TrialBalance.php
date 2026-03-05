<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class TrialBalance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Trial Balance';
    protected static ?string $title = 'Trial Balance';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.trial-balance';

    public ?string $as_of_date = null;

    public Collection $accounts;
    public float $totalDebits = 0;
    public float $totalCredits = 0;
    public bool $isBalanced = true;

    public function mount(): void
    {
        $this->as_of_date = now()->toDateString();
        $this->accounts = collect();
        $this->form->fill([
            'as_of_date' => $this->as_of_date,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        DatePicker::make('as_of_date')
                            ->label('As of Date')
                            ->native(false)
                            ->default(now())
                            ->required(),
                    ]),
            ])
            ->statePath('');
    }

    public function generate(): void
    {
        $allAccounts = Account::withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $orgId = auth()->check() ? auth()->user()->organization_id : null;
        $branchId = auth()->check() ? auth()->user()->branch_id : null;

        $results = collect();
        $this->totalDebits = 0;
        $this->totalCredits = 0;

        foreach ($allAccounts as $account) {
            $query = JournalEntryLine::withoutGlobalScopes()
                ->where('account_id', $account->id)
                ->whereHas('journalEntry', function ($q) use ($orgId, $branchId) {
                    $q->withoutGlobalScopes()
                        ->where('status', '!=', 'voided')
                        ->when($this->as_of_date, fn($q) => $q->whereDate('entry_date', '<=', $this->as_of_date))
                        ->when($orgId, fn($q) => $q->where('organization_id', $orgId))
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
                });

            $debits = (float) (clone $query)->where('type', 'debit')->sum('amount');
            $credits = (float) (clone $query)->where('type', 'credit')->sum('amount');

            if ($debits == 0 && $credits == 0) {
                continue; // Skip zero-balance accounts
            }

            $balance = $account->normal_balance === 'debit'
                ? ($debits - $credits)
                : ($credits - $debits);

            $debitBalance = $balance > 0 && $account->normal_balance === 'debit' ? $balance : ($balance < 0 && $account->normal_balance === 'credit' ? abs($balance) : ($account->normal_balance === 'debit' ? $balance : 0));
            $creditBalance = $balance > 0 && $account->normal_balance === 'credit' ? $balance : ($balance < 0 && $account->normal_balance === 'debit' ? abs($balance) : ($account->normal_balance === 'credit' ? $balance : 0));

            // Simplify: if balance is positive, it goes in the normal_balance column; if negative, it goes in the opposite column
            if ($balance >= 0) {
                $debitBalance = $account->normal_balance === 'debit' ? $balance : 0;
                $creditBalance = $account->normal_balance === 'credit' ? $balance : 0;
            } else {
                $debitBalance = $account->normal_balance === 'credit' ? abs($balance) : 0;
                $creditBalance = $account->normal_balance === 'debit' ? abs($balance) : 0;
            }

            $this->totalDebits += $debitBalance;
            $this->totalCredits += $creditBalance;

            $results->push([
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'debit_balance' => $debitBalance,
                'credit_balance' => $creditBalance,
            ]);
        }

        $this->accounts = $results;
        $this->isBalanced = abs($this->totalDebits - $this->totalCredits) < 0.01;
    }
}
