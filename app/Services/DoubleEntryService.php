<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Loan;
use App\Models\Repayments;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoubleEntryService
{
    /**
     * Generate a unique journal entry number.
     */
    public static function generateEntryNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $count = JournalEntry::withoutGlobalScopes()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return "JE-{$year}{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Find an account by code, ignoring global scopes (org scoping).
     */
    public static function findAccount(string $code): ?Account
    {
        return Account::withoutGlobalScopes()->where('code', $code)->first();
    }

    /**
     * Create a journal entry record and its lines atomically.
     *
     * @param array $entryData  Fields for journal_entries table
     * @param array $lines      Array of ['account_code', 'type', 'amount', 'description']
     */
    public static function createEntry(array $entryData, array $lines): ?JournalEntry
    {
        try {
            return DB::transaction(function () use ($entryData, $lines) {
                $orgId = auth()->check() ? auth()->user()->organization_id : null;
                $branchId = auth()->check() ? auth()->user()->branch_id : null;

                $entry = JournalEntry::create(array_merge([
                    'entry_number' => static::generateEntryNumber(),
                    'entry_date' => Carbon::today(),
                    'status' => 'posted',
                    'created_by' => auth()->id(),
                    'organization_id' => $orgId,
                    'branch_id' => $branchId,
                ], $entryData));

                foreach ($lines as $line) {
                    $account = static::findAccount($line['account_code']);
                    if (!$account) {
                        Log::warning("DoubleEntry: Account code [{$line['account_code']}] not found. Skipping line.");
                        continue;
                    }

                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $account->id,
                        'type' => $line['type'],
                        'amount' => $line['amount'],
                        'description' => $line['description'] ?? null,
                        'organization_id' => $orgId,
                        'branch_id' => $branchId,
                    ]);
                }

                return $entry;
            });
        } catch (\Throwable $e) {
            Log::error("DoubleEntry: Failed to create journal entry. " . $e->getMessage());
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // LOAN DISBURSEMENT
    // When a loan is approved/disbursed:
    //   DR  Loans Receivable       (1200)  — asset increases
    //   CR  Cash / Bank            (1010)  — asset decreases (we paid out)
    //   CR  Interest Income (if accrued) is handled on repayment
    // -------------------------------------------------------------------------
    public static function recordLoanDisbursement(Loan $loan): ?JournalEntry
    {
        $amount = (float) $loan->principal_amount;
        if ($amount <= 0) {
            return null;
        }

        return static::createEntry([
            'entry_date' => $loan->loan_release_date ?? Carbon::today(),
            'description' => "Loan disbursement – Loan #{$loan->loan_number} to {$loan->borrower?->first_name} {$loan->borrower?->last_name}",
            'source_type' => 'loan_disbursement',
            'source_id' => $loan->id,
            'source_model' => Loan::class,
            'reference' => $loan->loan_number,
        ], [
            [
                'account_code' => '1200',  // Loans Receivable
                'type' => 'debit',
                'amount' => $amount,
                'description' => "Principal disbursed for loan #{$loan->loan_number}",
            ],
            [
                'account_code' => '1010',  // Cash / Bank
                'type' => 'credit',
                'amount' => $amount,
                'description' => "Cash paid out for loan #{$loan->loan_number}",
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // LOAN REPAYMENT
    // When a repayment is recorded:
    //   DR  Cash / Bank            (1010)  — asset increases (we received cash)
    //   CR  Loans Receivable       (1200)  — asset decreases (principal portion)
    //   CR  Interest Income        (4100)  — revenue (interest portion)
    // -------------------------------------------------------------------------
    public static function recordLoanRepayment(Repayments $repayment): ?JournalEntry
    {
        $loan = $repayment->loan;
        if (!$loan) {
            return null;
        }

        $totalPayment = (float) $repayment->payments;
        if ($totalPayment <= 0) {
            return null;
        }

        // Calculate principal vs interest split
        $principal = (float) $loan->principal_amount;
        $totalRepayment = (float) ($loan->repayment_amount ?? $principal);
        $totalInterest = max(0, $totalRepayment - $principal);

        $ratio = $totalRepayment > 0 ? ($totalPayment / $totalRepayment) : 1;
        $principalPart = round($principal * $ratio, 2);
        $interestPart = round($totalPayment - $principalPart, 2);

        if ($interestPart < 0) {
            $principalPart = $totalPayment;
            $interestPart = 0;
        }

        $lines = [
            [
                'account_code' => '1010',  // Cash / Bank
                'type' => 'debit',
                'amount' => $totalPayment,
                'description' => "Repayment received for loan #{$loan->loan_number}",
            ],
            [
                'account_code' => '1200',  // Loans Receivable
                'type' => 'credit',
                'amount' => $principalPart,
                'description' => "Principal reduction for loan #{$loan->loan_number}",
            ],
        ];

        if ($interestPart > 0) {
            $lines[] = [
                'account_code' => '4100',  // Interest Income
                'type' => 'credit',
                'amount' => $interestPart,
                'description' => "Interest income for loan #{$loan->loan_number}",
            ];
        }

        return static::createEntry([
            'entry_date' => $repayment->created_at?->toDateString() ?? Carbon::today(),
            'description' => "Loan repayment – Loan #{$loan->loan_number}",
            'source_type' => 'loan_repayment',
            'source_id' => $repayment->id,
            'source_model' => Repayments::class,
            'reference' => $repayment->reference_number ?? $loan->loan_number,
        ], $lines);
    }

    // -------------------------------------------------------------------------
    // EXPENSE
    // When an expense is recorded:
    //   DR  Expense Account        (6xxx)  — expense increases
    //   CR  Cash / Bank            (1010)  — asset decreases (cash paid)
    // -------------------------------------------------------------------------
    public static function recordExpense(Expense $expense): ?JournalEntry
    {
        $amount = (float) $expense->expense_amount;
        if ($amount <= 0) {
            return null;
        }

        // Try to resolve expense account from category, else fall back to generic
        $expenseAccountCode = '6100'; // General Expenses (default)

        return static::createEntry([
            'entry_date' => $expense->expense_date ?? Carbon::today(),
            'description' => "Expense: {$expense->expense_name} – {$expense->expense_vendor}",
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'source_model' => Expense::class,
            'reference' => $expense->expense_name,
        ], [
            [
                'account_code' => $expenseAccountCode,
                'type' => 'debit',
                'amount' => $amount,
                'description' => "Expense: {$expense->expense_name}",
            ],
            [
                'account_code' => '1010',  // Cash / Bank
                'type' => 'credit',
                'amount' => $amount,
                'description' => "Cash paid for expense: {$expense->expense_name}",
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // PAYROLL
    // When payroll is run (payslip):
    //   DR  Salaries Expense       (6200)  — expense increases
    //   DR  Payroll Tax Expense    (6210)  — PAYE / Tax expense
    //   CR  Salaries Payable       (2100)  — liability (amount owed to employees)
    //   CR  Tax Payable            (2110)  — liability (tax owed to government)
    // -------------------------------------------------------------------------
    public static function recordPayroll(Payslip $payslip): ?JournalEntry
    {
        $grossPay = (float) ($payslip->basic_salary ?? 0);
        $netPay = (float) ($payslip->net_salary ?? 0);
        $paye = (float) ($payslip->paye ?? 0);
        $pension = (float) ($payslip->napsa ?? 0);

        if ($grossPay <= 0) {
            return null;
        }

        $taxTotal = $paye + $pension;
        $salaryPayable = $netPay;

        $lines = [
            [
                'account_code' => '6200', // Salaries Expense
                'type' => 'debit',
                'amount' => $grossPay,
                'description' => "Gross salary for {$payslip->employee?->first_name} {$payslip->employee?->last_name}",
            ],
            [
                'account_code' => '2100', // Salaries Payable
                'type' => 'credit',
                'amount' => $salaryPayable,
                'description' => "Net salary payable to {$payslip->employee?->first_name} {$payslip->employee?->last_name}",
            ],
        ];

        if ($paye > 0) {
            $lines[] = [
                'account_code' => '2110', // PAYE Payable
                'type' => 'credit',
                'amount' => $paye,
                'description' => "PAYE tax payable",
            ];
        }

        if ($pension > 0) {
            $lines[] = [
                'account_code' => '2120', // NAPSA Payable
                'type' => 'credit',
                'amount' => $pension,
                'description' => "NAPSA/pension payable",
            ];
        }

        return static::createEntry([
            'entry_date' => Carbon::today(),
            'description' => "Payroll for {$payslip->employee?->first_name} {$payslip->employee?->last_name}",
            'source_type' => 'payroll',
            'source_id' => $payslip->id,
            'source_model' => Payslip::class,
            'reference' => $payslip->id,
        ], $lines);
    }
}
