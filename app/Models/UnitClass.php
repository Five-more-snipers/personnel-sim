<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $casts = [
        'description' => 'string',
    ];

    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
