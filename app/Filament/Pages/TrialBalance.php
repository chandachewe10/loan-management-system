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

class TrialBalance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Trial Balance';
    protected static ?string $title = 'Trial Balance';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.trial-balance';

    // Form-bound property
    public ?string $as_of_date = null;

    // Display data — plain arrays for Livewire serialization
    public array $accountRows = [];
    public float $totalDebits = 0;
    public float $totalCredits = 0;
    public bool $isBalanced = true;
    public bool $hasGenerated = false;

    public function mount(): void
    {
        $this->as_of_date = now()->toDateString();
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
        $this->hasGenerated = true;

        $allAccounts = Account::withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $orgId = auth()->check() ? auth()->user()->organization_id : null;
        $branchId = auth()->check() ? auth()->user()->branch_id : null;

        $results = [];
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

            if ($balance >= 0) {
                $debitBalance = $account->normal_balance === 'debit' ? $balance : 0;
                $creditBalance = $account->normal_balance === 'credit' ? $balance : 0;
            } else {
                $debitBalance = $account->normal_balance === 'credit' ? abs($balance) : 0;
                $creditBalance = $account->normal_balance === 'debit' ? abs($balance) : 0;
            }

            $this->totalDebits += $debitBalance;
            $this->totalCredits += $creditBalance;

            $results[] = [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'debit_balance' => round($debitBalance, 2),
                'credit_balance' => round($creditBalance, 2),
            ];
        }

        $this->accountRows = $results;
        $this->isBalanced = abs($this->totalDebits - $this->totalCredits) < 0.01;
    }
}
