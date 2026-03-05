<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: recreate the table logic isn't needed here since
            // SQLite ignores foreign key details on column definitions.
            // Just rebuild with a raw approach — skip drop/change/re-add.
            DB::statement('PRAGMA foreign_keys=OFF');

            Schema::table('branches', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_manager')->nullable()->change();
            });

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropForeign(['branch_manager']);
                $table->unsignedBigInteger('branch_manager')->nullable()->change();
                $table->foreign('branch_manager')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            Schema::table('branches', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_manager')->nullable(false)->change();
            });

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropForeign(['branch_manager']);
                $table->unsignedBigInteger('branch_manager')->nullable(false)->change();
                $table->foreign('branch_manager')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};