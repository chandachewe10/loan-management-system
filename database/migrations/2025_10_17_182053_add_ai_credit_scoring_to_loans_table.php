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
            $table->decimal('ai_credit_score', 5, 2)->nullable()->after('loan_status');
            $table->decimal('default_probability', 5, 4)->nullable()->after('ai_credit_score');
            $table->json('risk_factors')->nullable()->after('default_probability');
            $table->string('ai_recommendation')->nullable()->after('risk_factors');
            $table->text('ai_decision_reason')->nullable()->after('ai_recommendation');
            $table->timestamp('ai_scored_at')->nullable()->after('ai_decision_reason');

            // Enhanced borrower data for AI analysis
            $table->decimal('borrower_monthly_income', 10, 2)->nullable()->after('ai_scored_at');
            $table->integer('borrower_employment_months')->nullable()->after('borrower_monthly_income');
            $table->decimal('borrower_existing_debts', 10, 2)->nullable()->after('borrower_employment_months');
            $table->integer('borrower_credit_history_months')->nullable()->after('borrower_existing_debts');
            $table->integer('borrower_previous_defaults')->default(0)->after('borrower_credit_history_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
