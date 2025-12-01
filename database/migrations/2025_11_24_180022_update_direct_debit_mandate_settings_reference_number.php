<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('direct_debit_mandate_settings')) {
            if (Schema::hasColumn('direct_debit_mandate_settings', 'service_provider_reference_format')) {
                // Drop the old column and add the new one
                Schema::table('direct_debit_mandate_settings', function (Blueprint $table) {
                    $table->dropColumn('service_provider_reference_format');
                });
                
                Schema::table('direct_debit_mandate_settings', function (Blueprint $table) {
                    $table->string('service_provider_reference_number')->nullable()->after('branch_id');
                });
            } elseif (!Schema::hasColumn('direct_debit_mandate_settings', 'service_provider_reference_number')) {
                Schema::table('direct_debit_mandate_settings', function (Blueprint $table) {
                    $table->string('service_provider_reference_number')->nullable()->after('branch_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('direct_debit_mandate_settings')) {
            if (Schema::hasColumn('direct_debit_mandate_settings', 'service_provider_reference_number')) {
                Schema::table('direct_debit_mandate_settings', function (Blueprint $table) {
                    $table->dropColumn('service_provider_reference_number');
                });
                
                Schema::table('direct_debit_mandate_settings', function (Blueprint $table) {
                    $table->string('service_provider_reference_format')->default('{loan_id}')->after('branch_id');
                });
            }
        }
    }
};
