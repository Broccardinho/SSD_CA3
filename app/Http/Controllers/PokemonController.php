<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;

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

        // Basic validation
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $results = Pokemon::where('name', 'like', "%$query%")
                ->orWhere('pokeapi_id', $query)
                ->select(['id', 'name', 'sprite_url', 'pokeapi_id'])
                ->limit(12)
                ->get();

            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
