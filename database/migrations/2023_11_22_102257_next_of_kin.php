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
        Schema::table('borrowers', function (Blueprint $table) {
            $table->string('next_of_kin_first_name')->nullable();
            $table->string('next_of_kin_last_name')->nullable();
            $table->string('phone_next_of_kin')->nullable();
            $table->string('address_next_of_kin')->nullable();
            $table->string('relationship_next_of_kin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            //
        });
    }
};
