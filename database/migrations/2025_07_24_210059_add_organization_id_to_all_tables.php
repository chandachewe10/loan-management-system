<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends \Illuminate\Database\Migrations\Migration {
   public function up()
{
    $tables = DB::select('SHOW TABLES');
    $dbName = 'Tables_in_' . DB::getDatabaseName();

    foreach ($tables as $table) {
        $tableName = $table->$dbName;

        // Skip unwanted tables
        if (in_array($tableName, ['migrations'])) {
            continue;
        }

        // Skip if the column already exists
        if (!Schema::hasColumn($tableName, 'organization_id')) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Check if the table has an 'id' column
                if (Schema::hasColumn($tableName, 'id')) {
                    $table->unsignedBigInteger('organization_id')->nullable()->after('id');
                } else {
                    // If no 'id', just add it without positioning
                    $table->unsignedBigInteger('organization_id')->nullable();
                }
            });
        }
    }
}


    public function down()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = 'Tables_in_' . DB::getDatabaseName();

        foreach ($tables as $table) {
            $tableName = $table->$dbName;

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
};
