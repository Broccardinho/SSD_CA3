<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\TeamController;

Auth::routes();

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pokedex', [PokemonController::class, 'index'])->name('pokedex');
Route::get('/pokedex/{id}', [PokemonController::class, 'show'])
    ->where('id', '[0-151]') // Only Gen 1 Pokémon IDs
    ->name('pokedex.show');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/builder', [TeamController::class, 'builder'])->name('builder');
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/add-pokemon', [TeamController::class, 'addPokemon'])
        ->name('teams.add-pokemon');
    Route::delete('/teams/{team}/remove-pokemon/{pokemon}', [TeamController::class, 'removePokemon'])
        ->name('teams.remove-pokemon');
});
