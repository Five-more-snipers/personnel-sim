<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $casts = [
        'description' => 'string',
    ];

    // Relasi balik: Satu Faction memiliki banyak Personnel
    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }

    // Relasi dengan SubGroup: Satu Faction memiliki banyak SubGroup
    public function subGroups()
    {
        return $this->hasMany(SubGroup::class);
    }
}
