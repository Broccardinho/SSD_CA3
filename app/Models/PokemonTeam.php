<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PokemonTeam extends Pivot
{
    protected $table = 'pokemon_team';
    protected $casts = [
        'moves' => 'array'
    ];
}
