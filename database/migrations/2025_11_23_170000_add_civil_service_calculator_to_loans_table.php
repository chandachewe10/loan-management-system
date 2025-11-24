<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Civil Service Loan Calculator Fields - Check if columns exist before adding
            if (!Schema::hasColumn('loans', 'basic_pay')) {
                $table->decimal('basic_pay', 15, 2)->nullable()->after('borrower_previous_defaults');
            }
            if (!Schema::hasColumn('loans', 'recurring_allowances')) {
                $table->json('recurring_allowances')->nullable()->after('basic_pay');
            }
            if (!Schema::hasColumn('loans', 'total_recurring_allowances')) {
                $table->decimal('total_recurring_allowances', 15, 2)->nullable()->after('recurring_allowances');
            }
            if (!Schema::hasColumn('loans', 'other_allowances')) {
                $table->json('other_allowances')->nullable()->after('total_recurring_allowances');
            }
            if (!Schema::hasColumn('loans', 'paye')) {
                $table->decimal('paye', 15, 2)->nullable()->after('other_allowances');
            }
            if (!Schema::hasColumn('loans', 'pension_napsa')) {
                $table->decimal('pension_napsa', 15, 2)->nullable()->after('paye');
            }
            if (!Schema::hasColumn('loans', 'health_insurance')) {
                $table->decimal('health_insurance', 15, 2)->nullable()->after('pension_napsa');
            }
            if (!Schema::hasColumn('loans', 'other_recurring_deductions')) {
                $table->json('other_recurring_deductions')->nullable()->after('health_insurance');
            }
            if (!Schema::hasColumn('loans', 'calculated_net_pay')) {
                $table->decimal('calculated_net_pay', 15, 2)->nullable()->after('other_recurring_deductions');
            }
            if (!Schema::hasColumn('loans', 'actual_net_pay_payslip')) {
                $table->decimal('actual_net_pay_payslip', 15, 2)->nullable()->after('calculated_net_pay');
            }
            if (!Schema::hasColumn('loans', 'qualification_status')) {
                $table->string('qualification_status')->nullable()->after('actual_net_pay_payslip');
            }
            if (!Schema::hasColumn('loans', 'qualification_notes')) {
                $table->text('qualification_notes')->nullable()->after('qualification_status');
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
                'basic_pay',
                'recurring_allowances',
                'total_recurring_allowances',
                'other_allowances',
                'paye',
                'pension_napsa',
                'health_insurance',
                'other_recurring_deductions',
                'calculated_net_pay',
                'actual_net_pay_payslip',
                'qualification_status',
                'qualification_notes',
            ];
            
            // Only drop columns that exist
            $existingColumns = array_filter($columns, fn($column) => Schema::hasColumn('loans', $column));
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};

