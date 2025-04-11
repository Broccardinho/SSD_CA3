<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PokemonService
{
    protected $baseUrl = 'https://pokeapi.co/api/v2';

    public function fetchFirstGenerationPokemon()
    {
        try {
            // First generation Pokemon are IDs 1-151
            $pokemonIds = range(1, 151);

            foreach ($pokemonIds as $id) {
                $this->fetchAndStorePokemon($id);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error fetching first generation Pokemon: " . $e->getMessage());
            return false;
        }
    }

    protected function fetchAndStorePokemon($id)
    {
        $response = Http::get("{$this->baseUrl}/pokemon/{$id}");

        if ($response->successful()) {
            $pokemonData = $response->json();

            $this->storePokemon($pokemonData);
        } else {
            Log::error("Failed to fetch Pokemon with ID: {$id}");
        }
    }

    protected function storePokemon(array $pokemonData)
    {

        $spriteUrl = $pokemonData['sprites']['other']['official-artwork']['front_default']
            ?? $pokemonData['sprites']['front_default'];

        Pokemon::updateOrCreate(
            ['pokeapi_id' => $pokemonData['id']],
            [
                'name' => $pokemonData['name'],
                'height' => $pokemonData['height'],
                'weight' => $pokemonData['weight'],
                'base_experience' => $pokemonData['base_experience'],
                'sprite_url' => $spriteUrl,
                'types' => $this->extractTypes($pokemonData['types']),
                'abilities' => $this->extractAbilities($pokemonData['abilities']),
                'stats' => $this->extractStats($pokemonData['stats']),
            ]
        );
    }

    protected function extractTypes(array $types): array
    {
        return array_map(function ($type) {
            return $type['type']['name'];
        }, $types);
    }

    protected function extractAbilities(array $abilities): array
    {
        return array_map(function ($ability) {
            return [
                'name' => $ability['ability']['name'],
                'is_hidden' => $ability['is_hidden'],
            ];
        }, $abilities);
    }

    protected function extractStats(array $stats): array
    {
        $result = [];

        foreach ($stats as $stat) {
            $result[$stat['stat']['name']] = $stat['base_stat'];
        }

        return $result;
    }
}
