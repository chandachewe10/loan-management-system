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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('organization_id')->nullable();
        $table->unsignedBigInteger('payer_id')->nullable();
        $table->decimal('payment_amount', 64, 0);
        $table->string('description')->nullable();
        $table->dateTime('payment_made_at');
        $table->dateTime('payment_expires_at');
        $table->string('transaction_reference')->nullable();
        $table->string('gateway')->nullable();
        $table->timestamps();
        $table->foreign('payer_id')->references('id')->on('users')->onDelete('set null');

    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
