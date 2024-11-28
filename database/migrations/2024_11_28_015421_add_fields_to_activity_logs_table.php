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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('ip_address', 45)->nullable()->after('user_id'); // Supports IPv4 and IPv6
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('action')->nullable()->after('user_agent');
            $table->text('details')->nullable()->after('action');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'ip_address', 'user_agent', 'action', 'details']);
        });
    }
};
