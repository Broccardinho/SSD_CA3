<?php
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg border-4 border-blue-800 p-6">
            <h1 class="text-2xl mb-4">Team Builder</h1>

            <!-- Team Display -->
            <div class="grid grid-cols-6 gap-4 mb-8" id="team-slots">
                @foreach($team->pokemon as $member)
                    <div class="team-slot bg-gray-100 p-4 rounded-lg border-2 border-gray-300">
                        <img src="{{ $member->sprite_url }}" class="w-full h-32 object-contain">
                        <div class="mt-2 text-center">
                            <h3 class="font-bold">{{ $member->name }}</h3>
                            <div class="text-sm">
                                @foreach($member->pivot->moves as $move)
                                    <div class="move-item">{{ $move }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Search and Add Pokémon -->
            <div class="bg-gray-100 p-4 rounded-lg">
                <input type="text" id="pokemonSearch" placeholder="Search Pokémon..."
                       class="w-full p-2 border-2 border-red-600 rounded-lg">
                <div id="searchResults" class="grid grid-cols-2 md:grid-cols-6 gap-2 mt-4"></div>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for search functionality -->
    <script>
        document.getElementById('pokemonSearch').addEventListener('input', function(e) {
            if (e.target.value.length > 2) {
                axios.get(`/pokedex/search?q=${e.target.value}`)
                    .then(response => {
                        const results = document.getElementById('searchResults');
                        results.innerHTML = response.data.map(pokemon => `
                    <div class="search-result bg-white p-2 rounded cursor-pointer hover:border-yellow-400 border-2"
                         data-id="${pokemon.id}"
                         onclick="addToTeam(${pokemon.id})">
                        <img src="${pokemon.sprite_url}" class="w-full h-16 object-contain">
                        <p class="text-center text-sm mt-1">${pokemon.name}</p>
                    </div>
                `).join('');
                    });
            }
        });

        function addToTeam(pokemonId) {
            axios.post(`/teams/{{ $team->id }}/add-pokemon`, {
                pokemon_id: pokemonId,
                moves: [],
                item: null
            }).then(response => {
                window.location.reload();
            });
        }
    </script>
@endsection
