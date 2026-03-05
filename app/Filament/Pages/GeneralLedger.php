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
use Illuminate\Support\Collection;

class GeneralLedger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'General Ledger';
    protected static ?string $title = 'General Ledger';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.general-ledger';

    public ?string $account_id = null;
    public ?string $date_from = null;
    public ?string $date_to = null;

    public Collection $lines;
    public float $openingBalance = 0;
    public float $runningBalance = 0;
    public float $totalDebits = 0;
    public float $totalCredits = 0;
    public ?Account $selectedAccount = null;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
        $this->lines = collect();
        $this->form->fill([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ]);
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

        $this->lines = $query->get();

        $this->totalDebits = (float) $this->lines->where('type', 'debit')->sum('amount');
        $this->totalCredits = (float) $this->lines->where('type', 'credit')->sum('amount');

        if ($this->account_id) {
            $this->selectedAccount = Account::withoutGlobalScopes()->find($this->account_id);
        } else {
            $this->selectedAccount = null;
        }

        // Compute running balance
        $balance = 0;
        $this->lines = $this->lines->map(function ($line) use (&$balance) {
            $account = $line->account;
            if ($account) {
                $isDebitNormal = $account->normal_balance === 'debit';
                if ($line->type === 'debit') {
                    $balance += $isDebitNormal ? $line->amount : -$line->amount;
                } else {
                    $balance += $isDebitNormal ? -$line->amount : $line->amount;
                }
            } else {
                $balance += $line->type === 'debit' ? $line->amount : -$line->amount;
            }
            $line->running_balance = $balance;
            return $line;
        });

        $this->runningBalance = $balance;
    }
}
