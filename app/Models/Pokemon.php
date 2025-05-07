<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $fillable = [
        'pokeapi_id',
        'name',
        'height',
        'weight',
        'base_experience',
        'sprite_url',
        'types',
        'abilities',
        'stats',
    ];

    protected $casts = [
        'types' => 'array',
        'abilities' => 'array',
        'stats' => 'array',
    ];
}
