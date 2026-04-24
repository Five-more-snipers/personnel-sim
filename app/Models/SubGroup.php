<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'faction_id', 'description'];

    protected $casts = [
        'description' => 'string',
    ];

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }

    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
