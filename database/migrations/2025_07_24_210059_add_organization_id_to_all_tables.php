<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up()
    {
        $tables = $this->getTables();

        foreach ($tables as $tableName) {
            if (in_array($tableName, ['migrations'])) {
                continue;
            }

            if (!Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'id')) {
                        $table->unsignedBigInteger('organization_id')->nullable()->after('id');
                    } else {
                        $table->unsignedBigInteger('organization_id')->nullable();
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = $this->getTables();

        foreach ($tables as $tableName) {
            if (in_array($tableName, ['migrations'])) {
                continue;
            }

            if (Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('organization_id');
                });
            }
        }
    }

    private function getTables(): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return array_column(
                DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"),
                'name'
            );
        }

        if ($driver === 'pgsql') {
            return array_column(
                DB::select("SELECT tablename AS name FROM pg_tables WHERE schemaname = 'public'"),
                'name'
            );
        }

        // MySQL / MariaDB
        $dbName = DB::getDatabaseName();
        $key = 'Tables_in_' . $dbName;
        return array_column(DB::select('SHOW TABLES'), $key);
    }
};