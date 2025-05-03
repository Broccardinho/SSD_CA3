<?php
// app/Models/Team.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'user_id', 'description', 'is_public'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

// In app/Models/Team.php
    public function pokemon()
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_team')
            ->using(TeamPokemon::class)
            ->withPivot(['moves', 'item', 'position'])
            ->withTimestamps();
    }
}

// app/Models/TeamPokemon.php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamPokemon extends Pivot
{
    protected $casts = [
        'moves' => 'array'
    ];
}
