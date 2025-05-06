<?php

namespace Database\Seeders;

use App\Models\Pokemon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class PokemonSeeder extends Seeder
{
    public function run()
    {
        // Fetch Gen 1 Pokémon (1–151)
        for ($id = 1; $id <= 151; $id++) {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
            if ($response->successful()) {
                $data = $response->json();
                Pokemon::updateOrCreate(
                    ['pokeapi_id' => $data['id']],
                    [
                        'name' => $data['name'],
                        'height' => $data['height'],
                        'weight' => $data['weight'],
                        'base_experience' => $data['base_experience'] ?? 0,
                        'sprite_url' => $data['sprites']['front_default'] ?? "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
                        'types' => json_encode(array_map(fn($type) => $type['type']['name'], $data['types'])),
                        'abilities' => json_encode(array_map(fn($ability) => $ability['ability']['name'], $data['abilities'])),
                        'stats' => json_encode(array_map(fn($stat) => [
                            'name' => $stat['stat']['name'],
                            'value' => $stat['base_stat']
                        ], $data['stats'])),
                    ]
                );
            } else {
                \Illuminate\Support\Facades\Log::error("Failed to fetch Pokémon ID {$id}");
            }
        }
    }
}
