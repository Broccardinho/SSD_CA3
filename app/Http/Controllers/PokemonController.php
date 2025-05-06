<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PokemonController extends Controller
{
    public function show($id)
    {
        $pokemon = Pokemon::findOrFail($id);
        return view('pokemon.show', compact('pokemon'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        Log::debug('Pokemon search query:', ['query' => $query]); // Debug: Log query

        if (strlen($query) < 2) {
            Log::debug('Query too short, returning empty results');
            return response()->json([]);
        }

        // Search by name (case-insensitive) or pokeapi_id
        $results = Pokemon::whereRaw('LOWER(name) LIKE ?', ["%$query%"])
            ->orWhere('pokeapi_id', $query)
            ->select(['id', 'name', 'sprite_url', 'pokeapi_id'])
            ->limit(12)
            ->get();

        Log::debug('Pokemon search results:', ['count' => $results->count(), 'results' => $results->toArray()]);

        return response()->json($results);
    }
}
