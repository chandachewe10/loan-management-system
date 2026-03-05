<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Default Chart of Accounts for a microfinance / lending institution.
     */
    public function run(): void
    {
        $accounts = [
            // ----------------------------------------------------------------
            // ASSETS (1xxx) — normal balance: DEBIT
            // ----------------------------------------------------------------
            ['code' => '1000', 'name' => 'Current Assets', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'All current asset accounts', 'is_system' => true],
            ['code' => '1010', 'name' => 'Cash and Bank', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Cash on hand and bank balances', 'parent_code' => '1000', 'is_system' => true],
            ['code' => '1011', 'name' => 'Petty Cash', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Petty cash fund', 'parent_code' => '1000'],
            ['code' => '1020', 'name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Amounts owed by customers', 'parent_code' => '1000'],
            ['code' => '1100', 'name' => 'Non-Current Assets', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'All non-current asset accounts', 'is_system' => true],
            ['code' => '1200', 'name' => 'Loans Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Outstanding loan principal owed by borrowers', 'parent_code' => '1100', 'is_system' => true],
            ['code' => '1210', 'name' => 'Loan Interest Receivable', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Accrued interest owed by borrowers', 'parent_code' => '1100'],
            ['code' => '1300', 'name' => 'Property, Plant & Equipment', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Fixed assets', 'parent_code' => '1100'],
            ['code' => '1310', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'normal_balance' => 'credit', 'description' => 'Contra-asset: depreciation on PP&E', 'parent_code' => '1100'],
            ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Expenses paid in advance', 'parent_code' => '1000'],

            // ----------------------------------------------------------------
            // LIABILITIES (2xxx) — normal balance: CREDIT
            // ----------------------------------------------------------------
            ['code' => '2000', 'name' => 'Current Liabilities', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'All current liability accounts', 'is_system' => true],
            ['code' => '2100', 'name' => 'Salaries Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Net salaries owed to employees', 'parent_code' => '2000', 'is_system' => true],
            ['code' => '2110', 'name' => 'PAYE Tax Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'PAYE income tax owed to ZRA', 'parent_code' => '2000', 'is_system' => true],
            ['code' => '2120', 'name' => 'NAPSA Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'NAPSA pension contributions payable', 'parent_code' => '2000', 'is_system' => true],
            ['code' => '2130', 'name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Amounts owed to suppliers', 'parent_code' => '2000'],
            ['code' => '2200', 'name' => 'Long-Term Liabilities', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Non-current liabilities', 'is_system' => true],
            ['code' => '2210', 'name' => 'Borrowings / Loans Payable', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Funds borrowed from external sources', 'parent_code' => '2200'],

            // ----------------------------------------------------------------
            // EQUITY (3xxx) — normal balance: CREDIT
            // ----------------------------------------------------------------
            ['code' => '3000', 'name' => "Owner's Equity", 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'All equity accounts', 'is_system' => true],
            ['code' => '3100', 'name' => 'Share Capital', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Paid-in share capital', 'parent_code' => '3000'],
            ['code' => '3200', 'name' => 'Retained Earnings', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Accumulated profits/losses', 'parent_code' => '3000'],
            ['code' => '3300', 'name' => 'Current Year Profit/Loss', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Net income for the current period', 'parent_code' => '3000'],

            // ----------------------------------------------------------------
            // REVENUE (4xxx) — normal balance: CREDIT
            // ----------------------------------------------------------------
            ['code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'All revenue accounts', 'is_system' => true],
            ['code' => '4100', 'name' => 'Interest Income', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Interest earned on loans', 'parent_code' => '4000', 'is_system' => true],
            ['code' => '4110', 'name' => 'Loan Processing Fees', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'One-time fees charged on loan origination', 'parent_code' => '4000'],
            ['code' => '4120', 'name' => 'Late Payment Penalties', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Penalty income from late repayments', 'parent_code' => '4000'],
            ['code' => '4200', 'name' => 'Other Income', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Miscellaneous income', 'parent_code' => '4000'],

            // ----------------------------------------------------------------
            // EXPENSES (6xxx) — normal balance: DEBIT
            // ----------------------------------------------------------------
            ['code' => '6000', 'name' => 'Operating Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'All operating expense accounts', 'is_system' => true],
            ['code' => '6100', 'name' => 'General Expenses', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Miscellaneous/general expenses', 'parent_code' => '6000', 'is_system' => true],
            ['code' => '6110', 'name' => 'Office Supplies', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Office stationery and supplies', 'parent_code' => '6000'],
            ['code' => '6120', 'name' => 'Rent Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Office/branch rental costs', 'parent_code' => '6000'],
            ['code' => '6130', 'name' => 'Utilities Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Electricity, water, internet', 'parent_code' => '6000'],
            ['code' => '6140', 'name' => 'Transport & Travel', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Travel and transportation costs', 'parent_code' => '6000'],
            ['code' => '6200', 'name' => 'Salaries & Wages', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Gross salary costs', 'parent_code' => '6000', 'is_system' => true],
            ['code' => '6210', 'name' => 'Employee Benefits', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Non-salary employee benefits', 'parent_code' => '6000'],
            ['code' => '6300', 'name' => 'Loan Loss Provision', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Provision for bad or doubtful loans', 'parent_code' => '6000'],
            ['code' => '6400', 'name' => 'Depreciation Expense', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Depreciation on fixed assets', 'parent_code' => '6000'],
            ['code' => '6500', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Marketing costs', 'parent_code' => '6000'],
            ['code' => '6600', 'name' => 'Bank Charges & Fees', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Banking and transaction fees', 'parent_code' => '6000'],
        ];

        // Build a code → id map for parent linking
        $codeToId = [];

        foreach ($accounts as $accountData) {
            $parentCode = $accountData['parent_code'] ?? null;
            unset($accountData['parent_code']);

            $accountData['parent_id'] = $parentCode ? ($codeToId[$parentCode] ?? null) : null;
            $accountData['is_system'] = $accountData['is_system'] ?? false;

            $account = Account::withoutGlobalScopes()->updateOrCreate(
                ['code' => $accountData['code']],
                $accountData
            );

            $codeToId[$account->code] = $account->id;
        }

        $this->command->info('✅ Chart of Accounts seeded successfully (' . count($accounts) . ' accounts).');
    }
}
