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
