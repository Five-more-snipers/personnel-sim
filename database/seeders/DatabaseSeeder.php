<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faction;
use App\Models\Rank;
use App\Models\UnitClass;
use App\Models\Weapon;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Kubu (Factions)
        $factions = [
            ['name' => 'Corrupted Crystals'],
            ['name' => 'MTF Resh-24'],
            ['name' => 'Task Force-27'],
        ];
        foreach ($factions as $faction) Faction::create($faction);

        // 2. Setup Pangkat (Ranks)
        $ranks = [
            ['name' => 'Private', 'level' => 1],
            ['name' => 'Sergeant', 'level' => 2],
            ['name' => 'Lieutenant', 'level' => 3],
            ['name' => 'Captain', 'level' => 4],
            ['name' => 'Commander', 'level' => 5],
            ['name' => 'Leader', 'level' => 6],
        ];
        foreach ($ranks as $rank) Rank::create($rank);

        // 3. Setup Class Personel (Unit Classes)
        $classes = [
            ['name' => 'Assault'],
            ['name' => 'Recon'],
            ['name' => 'Medic'],
            ['name' => 'Marauder'],
            ['name' => 'Repressor'],
        ];
        foreach ($classes as $cls) UnitClass::create($cls);

        // 4. Setup Senjata Utama (Weapons)
        $weapons = [
            ['name' => 'Pindad SS2-V5 A1', 'type' => 'Assault Rifle'],
            ['name' => 'Carvidal AR-47', 'type' => 'Assault Rifle'],
            ['name' => 'Licht MP12', 'type' => 'Assault Rifle'],
            ['name' => 'London Lethal L14A4', 'type' => 'Sniper Rifle'],
            ['name' => 'Kroshu V2', 'type' => 'Submachine Gun'],
            ['name' => 'RamGun DBB', 'type' => 'Shotgun'],
        ];
        foreach ($weapons as $weapon) Weapon::create($weapon);
    }
}