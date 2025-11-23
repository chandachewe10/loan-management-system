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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_representative')->nullable()->after('name');
            $table->string('company_phone')->nullable()->after('company_representative');
            $table->text('company_address')->nullable()->after('company_phone');
            $table->boolean('profile_completion_modal_shown')->default(false)->after('company_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_representative', 'company_phone', 'company_address', 'profile_completion_modal_shown']);
        });
    }
};
