<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if column doesn't exist first
        if (! Schema::hasColumn('weapons', 'description')) {
            Schema::table('weapons', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('weapons', 'description')) {
            Schema::table('weapons', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
