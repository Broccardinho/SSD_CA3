<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the team builder interface
     */
    public function builder()
    {
        $team = auth()->user()->teams()->firstOrCreate(['name' => 'My First Team']);
        return view('builder', [
            'team' => $team->load('pokemon')
        ]);
    }

    /**
     * Display a listing of the user's teams
     */
    public function index()
    {
        $teams = auth()->user()->teams()->with('pokemon')->latest()->get();
        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created team
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $team = auth()->user()->teams()->create($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    /**
     * Display the specified team
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);
        return view('teams.show', [
            'team' => $team->load('pokemon')
        ]);
    }

    /**
     * Show the form for editing the team
     */
    public function edit(Team $team)
    {
        $this->authorize('update', $team);
        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified team
     */
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Remove the specified team
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();
        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    /**
     * Add a Pokémon to the team
     */
    public function addPokemon(Team $team, Request $request)
    {
        $this->authorize('update', $team);

        $request->validate([
            'pokemon_id' => 'required|exists:pokemon,id',
            'moves' => 'array|max:4',
            'item' => 'string|nullable'
        ]);

        // Check team size limit (max 6 Pokémon)
        if ($team->pokemon()->count() >= 6) {
            return back()->with('error', 'Your team already has 6 Pokémon!');
        }

        // Check for duplicate Pokémon
        if ($team->pokemon()->where('pokemon_id', $request->pokemon_id)->exists()) {
            return back()->with('error', 'This Pokémon is already on your team!');
        }

        $team->pokemon()->attach($request->pokemon_id, [
            'moves' => $request->moves,
            'item' => $request->item
        ]);

        return back()->with('success', 'Pokémon added to team!');
    }

    /**
     * Remove a Pokémon from the team
     */
    public function removePokemon(Team $team, Pokemon $pokemon)
    {
        $this->authorize('update', $team);

        $team->pokemon()->detach($pokemon->id);

        return back()->with('success', 'Pokémon removed from team!');
    }

    /**
     * Update a Pokémon's data in the team
     */
    public function updatePokemon(Team $team, Pokemon $pokemon, Request $request)
    {
        $this->authorize('update', $team);

        $request->validate([
            'moves' => 'array|max:4',
            'item' => 'string|nullable'
        ]);

        $team->pokemon()->updateExistingPivot($pokemon->id, [
            'moves' => $request->moves,
            'item' => $request->item
        ]);

        return back()->with('success', 'Pokémon updated successfully!');
    }
}
