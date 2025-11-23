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
        Schema::table('branches', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['branch_manager']);
            
            // Make branch_manager nullable
            $table->unsignedBigInteger('branch_manager')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable support
            $table->foreign('branch_manager')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['branch_manager']);
            
            // Make branch_manager not nullable again
            $table->unsignedBigInteger('branch_manager')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('branch_manager')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
