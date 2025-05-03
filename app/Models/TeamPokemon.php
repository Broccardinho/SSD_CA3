<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamPokemon extends Pivot
{
    protected $casts = [
        'moves' => 'array',
        'position' => 'integer'
    ];

    protected $fillable = ['moves', 'item', 'position'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}
