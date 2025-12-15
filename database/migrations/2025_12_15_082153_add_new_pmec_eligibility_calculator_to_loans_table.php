<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add new PMEC Eligibility Calculator fields based on "ELIGILITY CALCULATOR plus (2).csv"
     * Formula: Maximum Allowable EMI = 60% of Monthly Pay
     * Eligible EMI = Maximum Allowable EMI - Existing Loans EMI
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // New Eligibility Calculator Fields
            if (!Schema::hasColumn('loans', 'monthly_pay')) {
                $table->decimal('monthly_pay', 15, 2)->nullable()->after('qualification_notes');
            }
            if (!Schema::hasColumn('loans', 'maximum_allowable_emi')) {
                $table->decimal('maximum_allowable_emi', 15, 2)->nullable()->after('monthly_pay');
            }
            if (!Schema::hasColumn('loans', 'existing_loans_emi')) {
                $table->decimal('existing_loans_emi', 15, 2)->nullable()->default(0)->after('maximum_allowable_emi');
            }
            if (!Schema::hasColumn('loans', 'eligible_emi')) {
                $table->decimal('eligible_emi', 15, 2)->nullable()->after('existing_loans_emi');
            }
            if (!Schema::hasColumn('loans', 'loan_amount_eligibility')) {
                $table->decimal('loan_amount_eligibility', 15, 2)->nullable()->after('eligible_emi');
            }
            if (!Schema::hasColumn('loans', 'eligibility_interest_rate')) {
                $table->decimal('eligibility_interest_rate', 5, 2)->nullable()->after('loan_amount_eligibility');
            }
            if (!Schema::hasColumn('loans', 'loan_period')) {
                $table->integer('loan_period')->nullable()->after('eligibility_interest_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = [
                'monthly_pay',
                'maximum_allowable_emi',
                'existing_loans_emi',
                'eligible_emi',
                'loan_amount_eligibility',
                'eligibility_interest_rate',
                'loan_period',
            ];
            
            // Only drop columns that exist
            $existingColumns = array_filter($columns, fn($column) => Schema::hasColumn('loans', $column));
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
