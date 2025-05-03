<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function builder()
    {
        $user = auth()->user();
        $team = $user->activeTeam ?? $user->teams()->create([
            'name' => $user->name . "'s Team",
            'is_active' => true
        ]);

        return view('builder', [
            'team' => $team->load(['pokemon']),
            'pokemons' => Pokemon::orderBy('name')->get()
        ]);
    }

    public function updateTeam(Request $request, Team $team)
    {
        $request->validate([
            'pokemons' => 'required|array|size:6',
            'pokemons.*.id' => 'required|exists:pokemon,id',
            'pokemons.*.position' => 'required|integer|min:0|max:5',
            'pokemons.*.moves' => 'nullable|array|max:4'
        ]);

        // Clear existing team
        $team->pokemon()->detach();

        // Add new pokemon with positions
        foreach ($request->pokemons as $slot) {
            $team->pokemon()->attach($slot['id'], [
                'position' => $slot['position'],
                'moves' => $slot['moves'] ?? []
            ]);
        }

        return response()->json(['success' => true]);
    }
}
