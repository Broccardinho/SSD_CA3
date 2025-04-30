<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pokemon()
    {
        return $this->belongsToMany(Pokemon::class, 'team_pokemon')
            ->withPivot(['moves', 'item'])
            ->withTimestamps();
    }
}
