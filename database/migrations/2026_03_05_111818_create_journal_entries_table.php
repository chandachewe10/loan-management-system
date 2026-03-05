<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // e.g. JE-2026-00001
            $table->date('entry_date');
            $table->string('description');
            $table->enum('source_type', [
                'loan_disbursement',
                'loan_repayment',
                'expense',
                'manual',
                'payroll',
                'transfer',
            ])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable(); // ID of the related loan/repayment/expense
            $table->string('source_model')->nullable(); // Fully qualified model class
            $table->enum('status', ['draft', 'posted', 'voided'])->default('posted');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
