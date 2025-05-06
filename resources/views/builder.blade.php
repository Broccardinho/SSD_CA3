@extends('layouts.app')

@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pokémon Team Builder | Gen 1</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <style>
        .pokedex-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }

        .pokemon-card {
            background: #ffffff;
            border: 4px solid #3B4CCA;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .pokemon-card:hover {
            transform: translateY(-2px);
            border-color: #FF0000;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        .pixel-sprite {
            image-rendering: pixelated;
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }

        .loading-pokeball {
            animation: spin 1s linear infinite;
            width: 60px;
            height: 60px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    <div class="bg-blue-100 min-h-screen" style="font-family: 'Press Start 2P', cursive;">
        <!-- Header Matching Home Menu -->
        <header class="bg-red-600 border-b-8 border-yellow-400 shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                             alt="Pokéball"
                             class="h-12 w-12 mr-4">
                        <h1 class="text-yellow-400 text-2xl md:text-3xl text-shadow">POKÉMON TEAM BUILDER</h1>
                    </div>
                    <nav class="flex space-x-2 md:space-x-6">
                        <a href="{{ route('home') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">HOME</a>
                        <a href="{{ route('pokedex') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">POKÉDEX</a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8">
            <!-- Team Display Section -->
            <div class="bg-white rounded-lg border-4 border-blue-800 p-6 mb-8">
                <h2 class="text-red-600 text-xl md:text-2xl mb-6 text-center">YOUR TEAM</h2>

                <div id="team-slots" class="grid grid-cols-3 gap-4">
                    @foreach (range(1, 6) as $slot)
                        <div class="team-slot" data-slot="{{ $slot }}">
                            @php
                                $pokemon = $team->pokemon->firstWhere('pivot.position', $slot);
                            @endphp
                            @if ($pokemon)
                                <div class="relative">
                                    <img src="{{ $pokemon->sprite_url }}"
                                         class="pixel-sprite"
                                         alt="{{ $pokemon->name }}">
                                    <button class="remove-pokemon absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                            data-pokemon-id="{{ $pokemon->id }}">
                                        <span class="text-xs">×</span>
                                    </button>
                                </div>
                                <p class="text-center text-sm mt-2">{{ strtoupper($pokemon->name) }}</p>
                            @else
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                                     class="pokeball-placeholder"
                                     alt="Empty Slot">
                                <p class="text-center text-sm mt-2 text-gray-500">Empty Slot</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-lg border-4 border-blue-800 p-4 mb-8">
                <div class="mb-6">
                    <input type="text"
                           id="pokemonSearch"
                           placeholder="SEARCH POKÉMON..."
                           class="w-full p-3 border-2 border-red-600 rounded-lg text-sm focus:outline-none focus:border-yellow-400"
                           style="font-family: 'Press Start 2P', cursive;">
                </div>

                <!-- Search Results Grid -->
                <div class="pokedex-grid" id="searchResults">
                    <!-- Results will appear here -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4">
                <button id="saveTeam"
                        class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg inline-block border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                    SAVE TEAM
                </button>
                <button id="clearTeam"
                        class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg inline-block border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                    CLEAR TEAM
                </button>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('pokemonSearch');
            const searchResults = document.getElementById('searchResults');
            const saveButton = document.getElementById('saveTeam');
            const clearButton = document.getElementById('clearTeam');

            // Debounce function for search
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }

            // Search Pokémon
            searchInput.addEventListener('input', debounce(function(e) {
                const query = e.target.value.trim();
                console.log('Search query:', query); // Debug: Log query
                searchResults.innerHTML = '';

                if (query.length < 2) {
                    searchResults.innerHTML = '<p class="text-gray-500 text-sm">Enter at least 2 characters to search.</p>';
                    return;
                }

                searchResults.innerHTML = `
            <div class="col-span-full text-center py-10">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                     alt="Loading"
                     class="loading-pokeball mx-auto">
                <p class="text-gray-600 mt-4 text-sm">SEARCHING...</p>
            </div>`;

                fetch(`/pokemon/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        console.log('Search response status:', response.status); // Debug: Log status
                        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                        return response.json();
                    })
                    .then(pokemonList => {
                        console.log('Search results:', pokemonList); // Debug: Log results
                        if (pokemonList.length === 0) {
                            searchResults.innerHTML = '<p class="text-gray-500 text-sm">No Pokémon found.</p>';
                            return;
                        }

                        searchResults.innerHTML = pokemonList.map(pokemon => `
                    <div class="pokemon-card" data-pokemon-id="${pokemon.id}">
                        <img src="${pokemon.sprite_url}"
                             class="pixel-sprite"
                             alt="${pokemon.name}">
                        <p class="text-xs mt-1">${pokemon.name.toUpperCase()}</p>
                        <p class="text-xxs">#${pokemon.pokeapi_id.toString().padStart(3, '0')}</p>
                    </div>
                `).join('');

                        // Add click handlers for Pokémon cards
                        document.querySelectorAll('.pokemon-card').forEach(card => {
                            card.addEventListener('click', function() {
                                const pokemonId = this.getAttribute('data-pokemon-id');
                                console.log('Adding Pokémon ID:', pokemonId); // Debug: Log ID
                                addPokemonToTeam(pokemonId);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<p class="text-red-500">Search failed. Please try again.</p>';
                    });
            }, 300));

            // Add Pokémon to team
            function addPokemonToTeam(pokemonId) {
                const slots = [...document.querySelectorAll('#team-slots > div')];
                slots.forEach(slot => {
                    const img = slot.querySelector('img');
                    console.log('Slot:', slot.getAttribute('data-slot'), 'Image:', img ? img.src : 'No image');
                });

                const emptySlot = slots.find(slot => {
                    const img = slot.querySelector('img');
                    return img && img.src.includes('poke-ball.png');
                });

                if (!emptySlot) {
                    alert('Your team is already full! Remove a Pokémon first.');
                    return;
                }

                const slotNumber = emptySlot.getAttribute('data-slot');
                console.log('Adding to slot:', slotNumber);

                fetch(`/teams/{{ $team->id }}/add-pokemon`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pokemon_id: pokemonId,
                        position: slotNumber
                    })
                })
                    .then(response => {
                        console.log('Add Pokémon response status:', response.status);
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Failed to add Pokémon'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Pokémon added successfully!');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error adding Pokémon to team');
                        }
                    })
                    .catch(error => {
                        console.error('Add Pokémon error:', error);
                        alert(error.message || 'Failed to add Pokémon');
                    });
            }

            // Remove Pokémon
            document.querySelectorAll('.remove-pokemon').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const pokemonId = this.getAttribute('data-pokemon-id');

                    if (confirm('Remove this Pokémon from your team?')) {
                        fetch(`/teams/{{ $team->id }}/remove-pokemon/${pokemonId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Failed to remove Pokémon');
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                } else {
                                    alert(data.message || 'Error removing Pokémon');
                                }
                            })
                            .catch(error => {
                                console.error('Remove Pokémon error:', error);
                                alert('Failed to remove Pokémon');
                            });
                    }
                });
            });

            // Clear team
            clearButton.addEventListener('click', function() {
                if (confirm('Clear your entire team?')) {
                    fetch(`/teams/{{ $team->id }}/clear`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to clear team');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Error clearing team');
                            }
                        })
                        .catch(error => {
                            console.error('Clear team error:', error);
                            alert('Failed to clear team');
                        });
                }
            });

            // Save team
            saveButton.addEventListener('click', function() {
                fetch(`/teams/{{ $team->id }}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to save team');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Team saved successfully!');
                        } else {
                            alert(data.message || 'Error saving team');
                        }
                    })
                    .catch(error => {
                        console.error('Save team error:', error);
                        alert('Failed to save team');
                    });
            });
        });
    </script>

    <style>
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .transition-transform {
            transition: transform 0.2s ease-in-out;
        }
    </style>
@endsection
