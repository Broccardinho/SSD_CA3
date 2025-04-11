<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Featured Pokémon
        $featuredPokemon = Pokemon::whereIn('pokeapi_id', [6, 9, 3, 25, 130, 65])->get();

        // Feature section Pokémon
        $charizard = Pokemon::where('pokeapi_id', 6)->first();
        $blastoise = Pokemon::where('pokeapi_id', 9)->first();
        $venusaur = Pokemon::where('pokeapi_id', 3)->first();

        // Example teams with real Pokémon data
        $exampleTeams = [
            [
                'id' => 1,
                'name' => 'KANTO STARTERS',
                'pokemon' => Pokemon::whereIn('pokeapi_id', [1, 4, 7])->get(),
                'user' => 'ASH'
            ],
            [
                'id' => 2,
                'name' => 'LEGENDARY TEAM',
                'pokemon' => Pokemon::whereIn('pokeapi_id', [144, 145, 146])->get(),
                'user' => 'GARY'
            ],
            [
                'id' => 3,
                'name' => 'POWER HOUSE',
                'pokemon' => Pokemon::whereIn('pokeapi_id', [6, 9, 3, 149])->get(),
                'user' => 'RED'
            ]
        ];

        return view('home', compact(
            'featuredPokemon',
            'charizard',
            'blastoise',
            'venusaur',
            'exampleTeams'
        ));
    }
}
