<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PokemonService;

class FetchFirstGenPokemonCommand extends Command
{
    protected $signature = 'pokemon:fetch-first-gen';
    protected $description = 'Fetch and store first generation Pokemon from PokeAPI';

    public function handle(PokemonService $pokemonService)
    {
        $this->info('Starting to fetch first generation Pokemon...');

        $success = $pokemonService->fetchFirstGenerationPokemon();

        if ($success) {
            $this->info('Successfully fetched and stored all first generation Pokemon!');
            return Command::SUCCESS;
        }

        $this->error('There was an error fetching Pokemon data. Check the logs for details.');
        return Command::FAILURE;
    }
}
