<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/', function () {
//    return view('home');
//});
//
//Route::get('/builder', function () {
//    return view('builder');
//})->name('builder');
//
Route::get('/pokedex', function () {
    return view('pokedex');
})->name('pokedex');

Route::get('/pokedex/{id}', function ($id) {
    return view('pokemon', ['id' => $id]);
});

