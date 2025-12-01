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
        Schema::create('direct_debit_mandate_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('service_provider_reference_number')->nullable()->comment('Service Provider Reference Number assigned by the bank');
            $table->integer('days_before_payment_date')->default(5);
            $table->integer('days_after_payment_date')->default(5);
            $table->string('default_payment_frequency')->default('M')->comment('D=Daily, W=Weekly, FN=Fortnightly, M=Monthly, Q=Quarterly, H=Half Yearly, A=Annually');
            $table->string('payment_date_calculation')->default('loan_release_date')->comment('loan_release_date, loan_due_date, or custom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_debit_mandate_settings');
    }
};

