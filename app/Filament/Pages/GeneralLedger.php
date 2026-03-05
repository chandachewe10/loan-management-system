<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class GeneralLedger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'General Ledger';
    protected static ?string $title = 'General Ledger';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.general-ledger';

    // Form-bound properties
    public ?string $account_id = null;
    public ?string $date_from = null;
    public ?string $date_to = null;

    // Display data — stored as plain arrays so Livewire can serialize them
    public array $ledgerLines = [];
    public float $openingBalance = 0;
    public float $runningBalance = 0;
    public float $totalDebits = 0;
    public float $totalCredits = 0;
    public ?string $selectedAccountName = null;
    public ?string $selectedAccountCode = null;
    public bool $hasGenerated = false;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Select::make('account_id')
                            ->label('Account')
                            ->options(
                                Account::withoutGlobalScopes()
                                    ->where('is_active', true)
                                    ->orderBy('code')
                                    ->get()
                                    ->mapWithKeys(fn($a) => [$a->id => "[{$a->code}] {$a->name}"])
                            )
                            ->searchable()
                            ->nullable()
                            ->placeholder('All Accounts'),

                        DatePicker::make('date_from')
                            ->label('From Date')
                            ->native(false)
                            ->default(now()->startOfMonth()),

                        DatePicker::make('date_to')
                            ->label('To Date')
                            ->native(false)
                            ->default(now()),
                    ]),
            ])
            ->statePath('');
    }

    public function generate(): void
    {
        $this->hasGenerated = true;

        $query = JournalEntryLine::withoutGlobalScopes()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', function ($q) {
                $q->withoutGlobalScopes()
                    ->where('status', '!=', 'voided')
                    ->when($this->date_from, fn($q) => $q->whereDate('entry_date', '>=', $this->date_from))
                    ->when($this->date_to, fn($q) => $q->whereDate('entry_date', '<=', $this->date_to))
                    ->when(
                        auth()->check(),
                        fn($q) => $q->where('organization_id', auth()->user()->organization_id)
                            ->where('branch_id', auth()->user()->branch_id)
                    );
            })
            ->when($this->account_id, fn($q) => $q->where('account_id', $this->account_id))
            ->orderBy('created_at');

        $lines = $query->get();

        $this->totalDebits = (float) $lines->where('type', 'debit')->sum('amount');
        $this->totalCredits = (float) $lines->where('type', 'credit')->sum('amount');

        if ($this->account_id) {
            $account = Account::withoutGlobalScopes()->find($this->account_id);
            $this->selectedAccountName = $account?->name;
            $this->selectedAccountCode = $account?->code;
        } else {
            $this->selectedAccountName = null;
            $this->selectedAccountCode = null;
        }

        // Convert to plain arrays and compute running balance
        $balance = 0;
        $this->ledgerLines = $lines->map(function ($line) use (&$balance) {
            $account = $line->account;
            if ($account) {
                $isDebitNormal = $account->normal_balance === 'debit';
                if ($line->type === 'debit') {
                    $balance += $isDebitNormal ? (float) $line->amount : -(float) $line->amount;
                } else {
                    $balance += $isDebitNormal ? -(float) $line->amount : (float) $line->amount;
                }
            } else {
                $balance += $line->type === 'debit' ? (float) $line->amount : -(float) $line->amount;
            }

            return [
                'entry_date' => $line->journalEntry?->entry_date?->format('d M Y') ?? '—',
                'entry_number' => $line->journalEntry?->entry_number ?? '—',
                'account_code' => $account?->code ?? '—',
                'account_name' => $account?->name ?? '—',
                'description' => $line->description ?? $line->journalEntry?->description ?? '',
                'type' => $line->type,
                'amount' => (float) $line->amount,
                'running_balance' => round($balance, 2),
            ];
        })->toArray();

        $this->runningBalance = round($balance, 2);
    }
}
