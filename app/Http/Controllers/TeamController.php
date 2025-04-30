<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Pokemon;
use App\Models\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // In your TeamController's builder method
    public function builder()
    {
        $team = auth()->user()->teams()->firstOrCreate(['name' => 'My First Team']);

        $team->load(['pokemon' => function($query) {
            $query->whereNotNull('id');
        }]);

        return view('builder', compact('team'));
    }

    public function addPokemon(Teams $team, Request $request)
    {
        $this->authorize('update', $team);

        $request->validate([
            'pokemon_id' => 'required|exists:pokemon,id',
            'moves' => 'array|max:4',
            'item' => 'string|nullable'
        ]);

        if ($team->pokemon()->count() >= 6) {
            return back()->with('error', 'Your team already has 6 Pokémon!');
        }

        if ($team->pokemon()->where('pokemon_id', $request->pokemon_id)->exists()) {
            return back()->with('error', 'This Pokémon is already on your team!');
        }

        $team->pokemon()->attach($request->pokemon_id, [
            'moves' => $request->moves ?? [],
            'item' => $request->item
        ]);

        return back()->with('success', 'Pokémon added to team!');
    }

    public function removePokemon(Teams $team, Pokemon $pokemon)
    {
        $this->authorize('update', $team);
        $team->pokemon()->detach($pokemon->id);
        return back()->with('success', 'Pokémon removed from team!');
    }
}
