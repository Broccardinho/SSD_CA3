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
        $query = $request->input('q');
        $pokemon = Pokemon::where('name', 'like', "%{$query}%")
            ->limit(12)
            ->get();

        return response()->json($pokemon);
    }
}
