<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    // Constants for featured Pokémon IDs
    const FEATURED_POKEMON_IDS = [6, 9, 3, 25, 130, 65]; // Charizard, Blastoise, Venusaur, Pikachu, Gyarados, Alakazam
    const STARTER_POKEMON_IDS = [1, 4, 7]; // Bulbasaur, Charmander, Squirtle
    const LEGENDARY_POKEMON_IDS = [144, 145, 146]; // Articuno, Zapdos, Moltres
    const POWERHOUSE_POKEMON_IDS = [6, 9, 3, 149]; // Charizard, Blastoise, Venusaur, Dragonite

    public function index()
    {
        // Get featured Pokémon without relationships
        $featuredPokemon = Pokemon::whereIn('pokeapi_id', self::FEATURED_POKEMON_IDS)->get();

        // Get starter Pokémon
        $charizard = Pokemon::where('pokeapi_id', 6)->first();
        $blastoise = Pokemon::where('pokeapi_id', 9)->first();
        $venusaur = Pokemon::where('pokeapi_id', 3)->first();

        // Example teams
        $exampleTeams = [
            $this->createExampleTeam('KANTO STARTERS', self::STARTER_POKEMON_IDS, 'ASH'),
            $this->createExampleTeam('LEGENDARY TEAM', self::LEGENDARY_POKEMON_IDS, 'GARY'),
            $this->createExampleTeam('POWER HOUSE', self::POWERHOUSE_POKEMON_IDS, 'RED')
        ];

        return view('home', compact(
            'featuredPokemon',
            'charizard',
            'blastoise',
            'venusaur',
            'exampleTeams'
        ));
    }

    /**
     * Get a Pokémon with caching
     */
    protected function getPokemonWithCache(int $pokeapiId)
    {
        return Cache::remember("pokemon_{$pokeapiId}", 3600, function () use ($pokeapiId) {
            return Pokemon::where('pokeapi_id', $pokeapiId)
                ->with(['types', 'stats']) // Eager load relationships
                ->firstOrFail();
        });
    }

    /**
     * Create an example team structure
     */
    protected function createExampleTeam(string $name, array $pokeapiIds, string $user): array
    {
        $pokemon = Pokemon::whereIn('pokeapi_id', $pokeapiIds)->get();

        return [
            'id' => crc32($name),
            'name' => $name,
            'pokemon' => $pokemon,
            'user' => $user
        ];
    }

    /**
     * Get recently created teams from real users
     */
    protected function getRecentTeams()
    {
        if (!class_exists(Team::class)) {
            return [];
        }

        return Cache::remember('recent_teams', 300, function () { // 5 minute cache
            return Team::with(['user', 'pokemon' => function ($query) {
                $query->with(['types'])->limit(6);
            }])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->name,
                        'pokemon' => $team->pokemon,
                        'user' => $team->user->name,
                        'created_at' => $team->created_at
                    ];
                })
                ->toArray();
        });
    }
}
