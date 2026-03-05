<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', [
                'asset',
                'liability',
                'equity',
                'revenue',
                'expense',
            ]);
            $table->enum('normal_balance', ['debit', 'credit']); // debit for assets/expenses, credit for liabilities/equity/revenue
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // system accounts cannot be deleted
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
