<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapon extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'inspiration_source', 'description'];

    protected $casts = [
        'description' => 'string',
        'inspiration_source' => 'string',
    ];

    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
