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
Schema::create('asset_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();           
    $table->string('code')->nullable();         
    $table->text('description')->nullable();    
    $table->decimal('default_depreciation_rate', 5, 2)->nullable(); 
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
