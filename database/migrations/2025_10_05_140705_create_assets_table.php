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
Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->string('asset_name');
    $table->string('asset_code')->unique();
    $table->foreignId('asset_category_id')->constrained()->cascadeOnDelete();
    $table->date('purchase_date');
    $table->decimal('purchase_cost', 15, 2);
    $table->string('supplier')->nullable();
    $table->integer('useful_life_years')->default(5);
    $table->enum('depreciation_method', ['straight_line', 'reducing_balance'])->default('straight_line');
    $table->decimal('depreciation_rate', 5, 2)->nullable();
    $table->decimal('accumulated_depreciation', 15, 2)->default(0);
    $table->decimal('net_book_value', 15, 2)->nullable();
    $table->string('location')->nullable();
    $table->string('custodian')->nullable();
    $table->enum('status', ['active', 'disposed', 'damaged'])->default('active');
    $table->date('disposal_date')->nullable();
    $table->decimal('disposal_value', 15, 2)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
