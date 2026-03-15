<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make service_fee and penalty_fee columns nullable so that
     * loan types without these fees can be saved without errors.
     */
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN, so we recreate the table approach
        // We do this carefully by checking what columns already exist first.
        
        // For SQLite we must use the workaround: alter columns individually isn't supported.
        // We'll use the doctrine approach via modifyColumn if available,
        // otherwise use raw SQL to rebuild.
        
        // The simplest safe approach for SQLite is to update existing rows with 0 defaults
        // and re-create columns that aren't nullable yet.
        
        // Check if the column is already nullable by trying to insert NULL
        // For SQLite, we'll use a migration that works with the current state:
        
        // First, set any existing NULL values to 0 to avoid issues
        if (Schema::hasColumn('loan_types', 'service_fee_percentage')) {
            DB::statement('UPDATE loan_types SET service_fee_percentage = 0 WHERE service_fee_percentage IS NULL');
        }
        if (Schema::hasColumn('loan_types', 'service_fee_custom_amount')) {
            DB::statement('UPDATE loan_types SET service_fee_custom_amount = 0 WHERE service_fee_custom_amount IS NULL');
        }
        if (Schema::hasColumn('loan_types', 'penalty_fee_percentage')) {
            DB::statement('UPDATE loan_types SET penalty_fee_percentage = 0 WHERE penalty_fee_percentage IS NULL');
        }
        if (Schema::hasColumn('loan_types', 'penalty_fee_custom_amount')) {
            DB::statement('UPDATE loan_types SET penalty_fee_custom_amount = 0 WHERE penalty_fee_custom_amount IS NULL');
        }
        if (Schema::hasColumn('loan_types', 'early_repayment_percent')) {
            DB::statement('UPDATE loan_types SET early_repayment_percent = 0 WHERE early_repayment_percent IS NULL');
        }
        
        // For SQLite, we need to rebuild the table to make columns nullable.
        // We'll use a temporary table approach.
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Get current columns of loan_types table
        $columns = DB::select("PRAGMA table_info('loan_types')");
        $hasEarlyRepayment = collect($columns)->where('name', 'early_repayment_percent')->count() > 0;
        $hasServiceFeeType = collect($columns)->where('name', 'service_fee_type')->count() > 0;
        $hasServiceFee = collect($columns)->where('name', 'service_fee')->count() > 0;
        $hasPenaltyFeeType = collect($columns)->where('name', 'penalty_fee_type')->count() > 0;
        
        // Rebuild the table with all columns nullable where appropriate
        DB::statement('CREATE TABLE IF NOT EXISTS "loan_types_new" (
            "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            "loan_name" varchar NOT NULL,
            "interest_rate" numeric(10,2) NOT NULL,
            "interest_cycle" varchar NOT NULL,
            ' . ($hasServiceFeeType ? '"service_fee_type" varchar NULL,' : '') . '
            "service_fee_percentage" numeric(64,2) NULL DEFAULT 0,
            "service_fee_custom_amount" numeric(64,2) NULL DEFAULT 0,
            ' . ($hasPenaltyFeeType ? '"penalty_fee_type" varchar NULL,' : '') . '
            "penalty_fee_percentage" numeric(64,2) NULL DEFAULT 0,
            "penalty_fee_custom_amount" numeric(64,2) NULL DEFAULT 0,
            ' . ($hasEarlyRepayment ? '"early_repayment_percent" numeric(8,2) NULL DEFAULT 0,' : '') . '
            ' . ($hasServiceFee ? '"service_fee" numeric(64,0) NULL DEFAULT 0,' : '') . '
            "organization_id" integer NULL,
            "branch_id" integer NULL,
            "created_at" datetime NULL,
            "updated_at" datetime NULL
        )');
        
        // Copy data
        $colsToCopy = ['id', 'loan_name', 'interest_rate', 'interest_cycle', 'created_at', 'updated_at'];
        if ($hasServiceFeeType) $colsToCopy[] = 'service_fee_type';
        $colsToCopy[] = 'service_fee_percentage';
        $colsToCopy[] = 'service_fee_custom_amount';
        if ($hasPenaltyFeeType) $colsToCopy[] = 'penalty_fee_type';
        $colsToCopy[] = 'penalty_fee_percentage';
        $colsToCopy[] = 'penalty_fee_custom_amount';
        if ($hasEarlyRepayment) $colsToCopy[] = 'early_repayment_percent';
        if ($hasServiceFee) $colsToCopy[] = 'service_fee';
        
        // Check which of these columns actually exist before including in copy
        $existingCols = collect($columns)->pluck('name')->toArray();
        $colsToCopy = array_filter($colsToCopy, fn($c) => in_array($c, $existingCols));
        
        $colList = implode(', ', array_map(fn($c) => '"' . $c . '"', $colsToCopy));
        DB::statement("INSERT INTO \"loan_types_new\" ({$colList}) SELECT {$colList} FROM \"loan_types\"");
        
        DB::statement('DROP TABLE "loan_types"');
        DB::statement('ALTER TABLE "loan_types_new" RENAME TO "loan_types"');
        
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot easily reverse nullable changes in SQLite
    }
};
