@extends('layouts.app')

@section('content')
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

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 mb-8" id="team-slots">
                    @for($i = 1; $i <= 6; $i++)
                        <div class="bg-gray-100 rounded-lg p-2 border-2 border-gray-300 hover:border-yellow-400 transition-colors text-center cursor-pointer"
                             data-slot="{{ $i }}">
                            @if(isset($team->pokemon[$i-1]))
                                @php $pokemon = $team->pokemon[$i-1]; @endphp
                                <img src="{{ $pokemon->sprite_url }}"
                                     class="w-full h-24 object-contain"
                                     alt="{{ $pokemon->name }}">
                                <p class="text-xs mt-2 text-gray-800">{{ strtoupper($pokemon->name) }}</p>
                                <button class="remove-pokemon mt-1 text-red-600 hover:text-red-800 text-xxs"
                                        data-pokemon-id="{{ $pokemon->id }}"
                                        data-slot="{{ $i }}">
                                    REMOVE
                                </button>
                            @else
                                <div class="h-24 flex items-center justify-center">
                                    <p class="text-gray-400 text-xs">SLOT #{{ $i }}</p>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-lg border-4 border-blue-800 p-6 mb-8">
                <div class="mb-6">
                    <input type="text"
                           id="pokemonSearch"
                           placeholder="SEARCH POKÉMON..."
                           class="w-full p-3 border-2 border-red-600 rounded-lg text-sm focus:outline-none focus:border-yellow-400"
                           style="font-family: 'Press Start 2P', cursive;">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4" id="searchResults">
                    <!-- Search results will appear here -->
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
            const teamSlots = document.getElementById('team-slots');
            const saveButton = document.getElementById('saveTeam');
            const clearButton = document.getElementById('clearTeam');

            // Search functionality
            searchInput.addEventListener('input', debounce(function(e) {
                const query = e.target.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }

                fetch(`/pokemon/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(pokemonList => {
                        searchResults.innerHTML = pokemonList.map(pokemon => `
                        <div class="pokemon-card cursor-pointer" data-pokemon-id="${pokemon.id}">
                            <img src="${pokemon.sprite_url}"
                                 class="pixel-sprite"
                                 alt="${pokemon.name}">
                            <p class="text-gray-800 text-xs mt-2">${pokemon.name.toUpperCase()}</p>
                            <p class="text-gray-500 text-xxs">#${pokemon.pokeapi_id.toString().padStart(3, '0')}</p>
                        </div>
                    `).join('');

                        // Add click event to search results
                        document.querySelectorAll('.pokemon-card').forEach(card => {
                            card.addEventListener('click', function() {
                                const pokemonId = this.getAttribute('data-pokemon-id');
                                addPokemonToTeam(pokemonId);
                            });
                        });
                    });
            }, 300));

            // Add Pokémon to team
            function addPokemonToTeam(pokemonId) {
                const emptySlot = document.querySelector('.pokemon-card:not(:has(img[src$=".png"]))');

                if (!emptySlot) {
                    alert('Your team is already full! Remove a Pokémon first.');
                    return;
                }

                const slotNumber = emptySlot.getAttribute('data-slot');

                fetch(`/teams/{{ $team->id }}/add-pokemon`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pokemon_id: pokemonId,
                        position: slotNumber
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error adding Pokémon to team');
                        }
                    });
            }

            // Remove Pokémon from team
            document.querySelectorAll('.remove-pokemon').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const pokemonId = this.getAttribute('data-pokemon-id');

                    if (confirm('Remove this Pokémon from your team?')) {
                        fetch(`/teams/{{ $team->id }}/remove-pokemon/${pokemonId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                }
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
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                }
            });

            // Save team
            saveButton.addEventListener('click', function() {
                fetch(`/teams/{{ $team->id }}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Team saved successfully!');
                        }
                    });
            });

            // Debounce function for search
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }
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
