<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class LinkExistingWalletsSeeder extends Seeder
{
    /**
     * Link existing wallets to sub-accounts under 1010 Cash and Bank.
     */
    public function run(): void
    {
        $parentAccount = Account::withoutGlobalScopes()->where('code', '1010')->first();

        if (!$parentAccount) {
            $this->command->error('❌ Parent account 1010 not found. Run ChartOfAccountsSeeder first.');
            return;
        }

        $wallets = Wallet::withoutGlobalScopes()->whereNull('account_id')->get();

        if ($wallets->isEmpty()) {
            $this->command->info('ℹ️  No unlinked wallets found. All wallets are already linked.');
            return;
        }

        $count = 0;
        foreach ($wallets as $wallet) {
            $subCode = '1010-W' . $wallet->id;

            // Check if sub-account already exists
            $existing = Account::withoutGlobalScopes()->where('code', $subCode)->first();

            if (!$existing) {
                $existing = Account::withoutGlobalScopes()->create([
                    'code' => $subCode,
                    'name' => 'Wallet: ' . $wallet->name,
                    'type' => 'asset',
                    'normal_balance' => 'debit',
                    'description' => 'Auto-linked account for wallet "' . $wallet->name . '"',
                    'is_active' => true,
                    'is_system' => true,
                    'parent_id' => $parentAccount->id,
                    'organization_id' => $wallet->organization_id,
                    'branch_id' => $wallet->branch_id,
                ]);
            }

            $wallet->account_id = $existing->id;
            $wallet->saveQuietly(); // Don't fire observer
            $count++;
        }

        $this->command->info("✅ Linked {$count} existing wallets to Chart of Accounts sub-accounts.");
    }
}
