<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_groups', function (Blueprint $table) {
            $table->foreignId('faction_id')->constrained()->onDelete('cascade')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('sub_groups', function (Blueprint $table) {
            $table->dropForeign(['faction_id']);
            $table->dropColumn('faction_id');
        });
    }
};