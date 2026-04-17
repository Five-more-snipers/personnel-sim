<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    // 1. Membuka gerbang agar form bisa menyimpan data ke kolom-kolom ini
    protected $fillable = [
        'name',
        'faction_id',
        'rank_id',
        'unit_class_id',
        'weapon_id',
    ];

    // 2. Mendefinisikan Relasi ke Tabel Master (Agar Index.vue bisa membaca nama)
    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    // Perhatikan penamaan fungsi ini (menggunakan camelCase)
    public function unitClass()
    {
        return $this->belongsTo(UnitClass::class);
    }

    public function weapon()
    {
        return $this->belongsTo(Weapon::class);
    }
}