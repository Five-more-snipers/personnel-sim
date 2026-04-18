<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->foreignId('sub_group_id')->nullable()->after('faction_id')->constrained('sub_groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropForeign(['sub_group_id']);
            $table->dropColumn('sub_group_id');
        });
    }
};