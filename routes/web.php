<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\TeamController;

// Authentication Routes
Auth::routes();

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pokedex', function () {
    return view('pokedex');
})->name('pokedex');
Route::get('/pokedex/{id}', [PokemonController::class, 'show'])->name('pokedex.show');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/builder', [TeamController::class, 'builder'])->name('builder');
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/add-pokemon', [TeamController::class, 'addPokemon'])->name('teams.add-pokemon');
    Route::delete('/teams/{team}/remove-pokemon/{pokemon}', [TeamController::class, 'removePokemon'])->name('teams.remove-pokemon');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/pokedex', function () {
    return view('pokedex');
})->name('pokedex');

Route::get('/pokedex/{id}', function ($id) {
    return view('pokemon-details', ['pokemonId' => $id]); // We'll create this view
})->name('pokemon.details');

// Team routes
Route::prefix('teams/{team}')->group(function() {
    Route::post('/add-pokemon', [TeamController::class, 'addPokemonWithPosition']);
    Route::delete('/remove-pokemon/{pokemon}', [TeamController::class, 'removePokemon']);
    Route::delete('/clear', [TeamController::class, 'clearTeam']);
    Route::put('/reorder', [TeamController::class, 'reorderTeam']);
});

Route::get('/pokemon/search', [PokemonController::class, 'search'])->name('pokemon.search');
