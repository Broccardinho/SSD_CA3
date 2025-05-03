<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['name', 'user_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class)
            ->withPivot(['moves', 'item', 'position'])
            ->orderBy('pivot_position');
    }
}
