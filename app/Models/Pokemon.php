<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pokemon extends Model
{
    protected $fillable = [
        'pokeapi_id',
        'name',
        'height',
        'weight',
        'base_experience',
        'sprite_url',
        // other fields...
    ];

    /**
     * The types that belong to the Pokémon
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'pokemon_type', 'pokemon_id', 'type_id')
            ->withTimestamps();
    }

    /**
     * The stats that belong to the Pokémon
     */
    public function stats(): BelongsToMany
    {
        return $this->belongsToMany(Stat::class, 'pokemon_stat', 'pokemon_id', 'stat_id')
            ->withPivot('value')
            ->withTimestamps();
    }
}
