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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('callsign')->nullable();

            $table->foreignID('faction_id')->constrained('factions')->onDelete('restrict');
            $table->foreignID('rank_id')->constrained('ranks')->onDelete('restrict');
            $table->foreignID('weapon_id')->constrained('weapons')->onDelete('restrict');
            $table->foreignID('unit_class_id')->constrained('unit_classes')->onDelete('restrict');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::dropIfExists('personnels');
    }
};
