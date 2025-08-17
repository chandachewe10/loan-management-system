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
        Schema::table('loan_types', function (Blueprint $table) {
            $table->string('service_fee_type')->nullable();
            $table->decimal('service_fee_percentage', 64, 2);
            $table->decimal('service_fee_custom_amount', 64, 2);
            $table->string('penalty_fee_type')->nullable();
            $table->decimal('penalty_fee_percentage', 64, 2);
            $table->decimal('penalty_fee_custom_amount', 64, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_types', function (Blueprint $table) {
            //
        });
    }
};
