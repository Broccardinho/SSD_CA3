<?php
// app/Models/Team.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pokemon()
    {
        return $this->belongsToMany(Pokemon::class)
            ->using(TeamPokemon::class)
            ->withPivot(['moves', 'item']);
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
