<?php

// app/Http/Controllers/TeamController.php
namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Pokemon;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function builder()
    {
        $team = auth()->user()->teams()->firstOrCreate(['name' => 'My First Team']);
        return view('builder', [
            'team' => $team->load('pokemon')
        ]);
    }

    public function addPokemon(Team $team, Request $request)
    {
        $request->validate([
            'pokemon_id' => 'required|exists:pokemon,id',
            'moves' => 'array|max:4',
            'item' => 'string|nullable'
        ]);

        $team->pokemon()->attach($request->pokemon_id, [
            'moves' => $request->moves,
            'item' => $request->item
        ]);

        return back()->with('success', 'Pokémon added to team!');
    }

    // Add other CRUD methods
}
