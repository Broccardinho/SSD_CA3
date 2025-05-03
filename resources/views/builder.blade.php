@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Pokémon Pool -->
            <div class="w-full md:w-1/3 bg-white rounded-lg border-4 border-blue-800 p-4">
                <h2 class="text-red-600 text-xl mb-4">Pokémon Pool</h2>
                <div class="grid grid-cols-2 gap-2" id="pokemon-pool">
                    @foreach($pokemons as $pokemon)
                        <div class="pokemon-card bg-gray-100 p-2 rounded border-2 border-gray-300 cursor-move hover:border-yellow-400 transition-colors"
                             draggable="true"
                             data-pokemon-id="{{ $pokemon->id }}">
                            <img src="{{ $pokemon->sprite_url }}"
                                 alt="{{ $pokemon->name }}"
                                 class="w-full h-16 object-contain">
                            <p class="text-center text-xs mt-1">{{ $pokemon->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Team Slots -->
            <div class="w-full md:w-2/3 bg-white rounded-lg border-4 border-red-600 p-4">
                <h2 class="text-red-600 text-xl mb-4">Your Team ({{ $team->name }})</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="team-slots">
                    @for($i = 0; $i < 6; $i++)
                        @php $pokemon = $team->pokemon->where('pivot.position', $i)->first(); @endphp
                        <div class="team-slot min-h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-400 flex items-center justify-center"
                             data-slot="{{ $i }}">
                            @if($pokemon)
                                <div class="pokemon-card bg-white p-2 rounded border-2 border-gray-300 cursor-move hover:border-yellow-400 transition-colors"
                                     draggable="true"
                                     data-pokemon-id="{{ $pokemon->id }}">
                                    <img src="{{ $pokemon->sprite_url }}"
                                         alt="{{ $pokemon->name }}"
                                         class="w-full h-16 object-contain">
                                    <p class="text-center text-xs mt-1">{{ $pokemon->name }}</p>
                                </div>
                            @else
                                <span class="text-gray-500 text-xs">Drop Pokémon Here</span>
                            @endif
                        </div>
                    @endfor
                </div>
                <button id="save-team"
                        class="mt-4 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                    SAVE TEAM
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const teamSlots = document.querySelectorAll('.team-slot');
            const saveButton = document.getElementById('save-team');
            let currentTeam = {};

            // Initialize current team from existing data
            @foreach($team->pokemon as $pokemon)
                currentTeam[{{ $pokemon->pivot->position }}] = {
                id: {{ $pokemon->id }},
                moves: {!! json_encode($pokemon->pivot->moves) !!},
                item: "{{ $pokemon->pivot->item }}"
            };
            @endforeach

            // Set up drag and drop
            document.querySelectorAll('.pokemon-card').forEach(card => {
                card.addEventListener('dragstart', dragStart);
            });

            teamSlots.forEach(slot => {
                slot.addEventListener('dragover', dragOver);
                slot.addEventListener('drop', drop);
            });

            function dragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.pokemonId);
                e.dataTransfer.effectAllowed = 'move';
            }

            function dragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            function drop(e) {
                e.preventDefault();
                const pokemonId = e.dataTransfer.getData('text/plain');
                const slotIndex = e.currentTarget.dataset.slot;

                // Update visual
                const pokemonCard = document.querySelector(`.pokemon-card[data-pokemon-id="${pokemonId}"]`);
                e.currentTarget.innerHTML = '';
                e.currentTarget.appendChild(pokemonCard.cloneNode(true));

                // Update current team data
                currentTeam[slotIndex] = {
                    id: pokemonId,
                    moves: [],
                    item: null
                };
            }

            // Save team
            saveButton.addEventListener('click', async function() {
                const teamData = [];
                for (let i = 0; i < 6; i++) {
                    if (currentTeam[i]) {
                        teamData.push({
                            id: currentTeam[i].id,
                            position: i,
                            moves: currentTeam[i].moves || [],
                            item: currentTeam[i].item || null
                        });
                    }
                }

                try {
                    const response = await fetch('{{ route('teams.update', $team->id) }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            pokemons: teamData
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert('Team saved successfully!');
                    } else {
                        alert('Error saving team');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error saving team');
                }
            });
        });
    </script>
@endsection
