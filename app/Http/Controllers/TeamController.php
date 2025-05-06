<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $team = auth()->user()->teams()->firstOrCreate([
            'name' => 'My First Team'
        ], [
            'description' => 'My starter Pokémon team',
            'is_public' => false
        ]);

        $team->load('pokemon');
        \Illuminate\Support\Facades\Log::debug('Team Pokémon after load:', [
            'team_id' => $team->id,
            'pokemon_count' => $team->pokemon->count(),
            'pokemon' => $team->pokemon->toArray()
        ]);

        return view('builder', [
            'team' => $team
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
        Log::debug('Authenticated user:', ['user_id' => auth()->id()]);
        Log::debug('Team being accessed:', ['team_id' => $team->id, 'user_id' => $team->user_id]);
         $this->authorize('update', $team);

        $request->validate([
            'pokemon_id' => 'required|exists:pokemon,id',
            'position' => 'required|integer|between:1,6',
        ]);

        if ($team->pokemon()->count() >= 6) {
            return response()->json([
                'success' => false,
                'message' => 'Your team is already full!'
            ], 400);
        }

        if ($team->pokemon()->wherePivot('position', $request->position)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This team slot is already occupied!'
            ], 400);
        }

        if ($team->pokemon()->where('pokemon_id', $request->pokemon_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This Pokémon is already on your team!'
            ], 400);
        }

        $team->pokemon()->attach($request->pokemon_id, [
            'position' => $request->position,
            'moves' => [],
            'item' => null
        ]);

        \Illuminate\Support\Facades\Log::debug('Pokémon added to team:', [
            'team_id' => $team->id,
            'pokemon_id' => $request->pokemon_id,
            'position' => $request->position
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove a Pokémon from the team
     */
    public function removePokemon(Team $team, Pokemon $pokemon)
    {
        $this->authorize('update', $team);

        $team->pokemon()->detach($pokemon->id);

        \Illuminate\Support\Facades\Log::debug('Pokémon removed from team:', [
            'team_id' => $team->id,
            'pokemon_id' => $pokemon->id
        ]);

        return response()->json(['success' => true]);
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

    // Add to TeamController
    /**
     * Add a Pokémon to the team with position
     */
    public function addPokemonWithPosition(Team $team, Request $request)
    {
        $this->authorize('update', $team);

        $request->validate([
            'pokemon_id' => 'required|exists:pokemon,id',
            'position' => 'required|integer|between:1,6'
        ]);

        // Check if position is already taken
        if ($team->pokemon()->wherePivot('position', $request->position)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This team slot is already occupied!'
            ], 400);
        }

        // Check if Pokémon is already on team
        if ($team->pokemon()->where('pokemon_id', $request->pokemon_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This Pokémon is already on your team!'
            ], 400);
        }

        $team->pokemon()->attach($request->pokemon_id, [
            'position' => $request->position,
            'moves' => [],
            'item' => null
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear the entire team
     */
    public function clearTeam(Team $team): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $team);

        $pokemonCountBefore = $team->pokemon()->count();
        $team->pokemon()->detach();
        $pokemonCountAfter = $team->pokemon()->count();

        \Illuminate\Support\Facades\Log::debug('Clear team executed:', [
            'team_id' => $team->id,
            'pokemon_count_before' => $pokemonCountBefore,
            'pokemon_count_after' => $pokemonCountAfter
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Reorder Pokémon in the team
     */
    public function reorderTeam(Team $team, Request $request)
    {
        $this->authorize('update', $team);

        $request->validate([
            'order' => 'required|array|size:6',
            'order.*.pokemon_id' => 'nullable|exists:pokemon,id',
            'order.*.position' => 'required|integer|between:1,6'
        ]);

        // Clear all current positions
        $team->pokemon()->detach();

        // Add Pokémon with new positions
        foreach ($request->order as $slot) {
            if ($slot['pokemon_id']) {
                $team->pokemon()->attach($slot['pokemon_id'], [
                    'position' => $slot['position'],
                    'moves' => [],
                    'item' => null
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function save(Team $team)
    {
        $this->authorize('update', $team);
        return response()->json(['success' => true]);
    }


}
