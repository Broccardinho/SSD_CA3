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
    Route::post('/teams/{team}/add-pokemon', [TeamController::class, 'addPokemon'])->middleware('auth')->name('teams.add-pokemon');
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
Route::prefix('teams/{team}')->middleware('auth')->group(function() {
    Route::post('/add-pokemon', [App\Http\Controllers\TeamController::class, 'addPokemon'])->name('teams.add-pokemon');
    Route::delete('/remove-pokemon/{pokemon}', [App\Http\Controllers\TeamController::class, 'removePokemon'])->name('teams.remove-pokemon');
    Route::delete('/clear', [App\Http\Controllers\TeamController::class, 'clearTeam'])->name('teams.clear');
    Route::put('/reorder', [App\Http\Controllers\TeamController::class, 'reorderTeam'])->name('teams.reorder');
});

Route::get('/pokemon/search', [App\Http\Controllers\PokemonController::class, 'search'])->name('pokemon.search');

Route::put('/teams/{team}', [App\Http\Controllers\TeamController::class, 'save'])->middleware('auth')->name('teams.save');
