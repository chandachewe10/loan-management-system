<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Make expense_attachment nullable since it's managed by Spatie MediaLibrary
     * and should not be a required column on the expenses table.
     */
    public function up(): void
    {
        // For SQLite, we need to rebuild the table
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Get current columns
        $columns = DB::select("PRAGMA table_info('expenses')");
        $existingCols = collect($columns)->pluck('name')->toArray();
        
        DB::statement('CREATE TABLE IF NOT EXISTS "expenses_new" (
            "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            "expense_name" varchar NOT NULL,
            "expense_amount" varchar NOT NULL,
            "expense_vendor" varchar NOT NULL,
            "expense_attachment" varchar NULL,
            "expense_date" varchar NOT NULL,
            "category_id" integer NOT NULL,
            "organization_id" integer NULL,
            "branch_id" integer NULL,
            "from_this_account" integer NULL,
            "created_at" datetime NULL,
            "updated_at" datetime NULL,
            FOREIGN KEY("category_id") REFERENCES "expense_categories"("id")
        )');
        
        // Copy columns that exist in both tables
        $newCols = ['id', 'expense_name', 'expense_amount', 'expense_vendor', 'expense_attachment', 
                    'expense_date', 'category_id', 'created_at', 'updated_at'];
        
        // Add optional cols if they exist
        $optionalCols = ['organization_id', 'branch_id', 'from_this_account'];
        foreach ($optionalCols as $col) {
            if (in_array($col, $existingCols)) {
                $newCols[] = $col;
            }
        }
        
        // Filter to only existing columns
        $colsToCopy = array_filter($newCols, fn($c) => in_array($c, $existingCols));
        $colList = implode(', ', array_map(fn($c) => '"' . $c . '"', $colsToCopy));
        
        DB::statement("INSERT INTO \"expenses_new\" ({$colList}) SELECT {$colList} FROM \"expenses\"");
        DB::statement('DROP TABLE "expenses"');
        DB::statement('ALTER TABLE "expenses_new" RENAME TO "expenses"');
        
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
