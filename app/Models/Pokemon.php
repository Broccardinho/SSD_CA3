<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'id', // Using PokéAPI IDs
        'name',
        'sprite_url',
        'types',
        'stats'
    ];

    protected $casts = [
        'types' => 'array',
        'stats' => 'array'
    ];
}
