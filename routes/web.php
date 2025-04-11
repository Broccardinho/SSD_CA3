<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PokemonController; // Ensure this is imported

// Root route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Builder route
Route::get('/builder', function () {
    return view('builder');
})->name('builder');

// Pokédex route
Route::get('/pokedex', function () {
    return view('pokedex');
})->name('pokedex');

// Pokémon detail route (dynamic ID)
Route::get('/pokedex/{id}', [PokemonController::class, 'show'])->name('pokedex.show');

// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::resource('teams', TeamController::class);
    Route::get('/builder', [TeamController::class, 'builder'])->name('builder');
    Route::post('/teams/{team}/add-pokemon', [TeamController::class, 'addPokemon'])->name('teams.add-pokemon');
    Route::delete('/teams/{team}/remove-pokemon/{pokemon}', [TeamController::class, 'removePokemon'])->name('teams.remove-pokemon');
});
