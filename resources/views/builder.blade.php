@extends('layouts.app')

@section('content')
    <main class="container mx-auto px-4 py-8">
        <!-- Team Builder Header -->
        <div class="bg-white rounded-lg border-4 border-blue-800 p-6 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-red-600 text-xl md:text-2xl mb-4">TEAM BUILDER</h2>
                <p class="text-gray-800 text-sm md:text-base mb-6">
                    Assemble your dream team from the original 151 Pokémon!
                </p>
            </div>

            <!-- Team Display Slots -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8" id="team-slots">
                @for($i = 0; $i < 6; $i++)
                    <div class="team-slot bg-gray-100 rounded-lg p-4 border-2 border-gray-300 min-h-[200px]
                            hover:border-yellow-400 transition-colors relative">
                        @if(isset($team->pokemon[$i]))
                            <div class="text-center">
                                <img src="{{ $team->pokemon[$i]->sprite_url }}"
                                     alt="{{ $team->pokemon[$i]->name }}"
                                     class="w-full h-24 object-contain mb-2">
                                <h3 class="text-sm font-bold text-gray-800">
                                    {{ strtoupper($team->pokemon[$i]->name) }}
                                </h3>
                                <button class="absolute top-1 right-1 text-red-600 hover:text-red-800 text-xs"
                                        onclick="removePokemon({{ $team->pokemon[$i]->id }})">
                                    ✕
                                </button>
                            </div>
                        @else
                            <div class="text-gray-400 text-center h-full flex items-center justify-center">
                                <span class="text-xs">EMPTY SLOT</span>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>

            <!-- Search Section -->
            <div class="bg-yellow-100 rounded-lg border-4 border-yellow-300 p-6">
                <div class="mb-4">
                    <input type="text"
                           id="pokemonSearch"
                           placeholder="SEARCH POKÉMON..."
                           class="w-full p-3 border-2 border-red-600 rounded-lg text-sm
                              focus:outline-none focus:border-yellow-400">
                </div>

                <!-- Search Results Grid -->
                <div id="searchResults" class="grid grid-cols-2 md:grid-cols-6 gap-4"></div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('pokemonSearch');

            searchInput.addEventListener('input', function(e) {
                if (e.target.value.length > 1) {
                    searchPokemon(e.target.value);
                }
            });

            function searchPokemon(query) {
                axios.get(`/pokedex/search?q=${query}&limit=151`)
                    .then(response => {
                        const results = document.getElementById('searchResults');
                        results.innerHTML = response.data.map(pokemon => `
                        <div class="bg-gray-100 rounded-lg p-2 border-2 border-gray-300
                                  hover:border-yellow-400 transition-colors text-center cursor-pointer"
                             onclick="addToTeam(${pokemon.id})">
                            <img src="${pokemon.sprite_url}"
                                 alt="${pokemon.name}"
                                 class="w-full h-24 object-contain">
                            <p class="text-xs mt-2 text-gray-800">${pokemon.name.toUpperCase()}</p>
                        </div>
                    `).join('');
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }
        });

        function addToTeam(pokemonId) {
            axios.post(`/teams/{{ $team->id }}/add-pokemon`, {
                pokemon_id: pokemonId
            }).then(response => {
                window.location.reload();
            }).catch(error => {
                alert('Error adding Pokémon: ' + error.response.data.message);
            });
        }

        function removePokemon(pokemonId) {
            if (confirm('Remove this Pokémon from your team?')) {
                axios.delete(`/teams/{{ $team->id }}/remove-pokemon/${pokemonId}`)
                    .then(response => {
                        window.location.reload();
                    })
                    .catch(error => {
                        alert('Error removing Pokémon: ' + error.response.data.message);
                    });
            }
        }
    </script>
@endsection
