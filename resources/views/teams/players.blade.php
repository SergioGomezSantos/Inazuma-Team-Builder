<x-app-layout>
    @section('title', $team->name . ' - Jugadores')

    @php
        $statLabels = [
            'GP' => 'PE',
            'TP' => 'PT',
            'Kick' => 'Tiro',
            'Body' => 'Físico',
            'Control' => 'Control',
            'Guard' => 'Defensa',
            'Speed' => 'Rapidez',
            'Stamina' => 'Aguante',
            'Guts' => 'Valor',
            'Freedom' => 'Libertad',
        ];

        $playersData = $players->mapWithKeys(function ($player) {
            return [
                $player->id => [
                    'name' => $player->name,
                    'full_name' => $player->full_name,
                    'image' => asset('storage/players/' . $player->image),
                    'position' => $player->position,
                    'element' => $player->element,
                    'original_team' => $player->original_team,
                    'stats' => $player->stats->groupBy('version')->map->first()->toArray(),
                    'techniques' => $player->techniques
                        ->groupBy(function ($tech) {
                            return $tech->pivot->source;
                        })
                        ->map(function ($group) {
                            return $group
                                ->map(function ($tech) {
                                    return [
                                        'id' => $tech->id,
                                        'name' => $tech->name,
                                        'type' => $tech->type,
                                        'element' => $tech->element,
                                        'with' => json_decode($tech->pivot->with, true) ?: [],
                                    ];
                                })
                                ->toArray();
                        })
                        ->toArray(),
                ],
            ];
        });
    @endphp

    @section('scripts')
        <script>
            window.teamPlayersData = @json($playersData);
            window.teamStatLabels = @json($statLabels);
            window.teamFirstPlayerId = {{ $players->first()->id }};
        </script>

        @vite(['resources/js/app.js'])
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div id="team-players-data" class="flex flex-col md:flex-row gap-6 h-[calc(100vh-12rem)]">
                    <!-- Left Panel - Players Data -->
                    <div class="w-full md:w-4/5 flex flex-col h-full min-h-0 gap-4">
                        <div class="w-full flex flex-col bg-gray-100 dark:bg-gray-700 p-4 rounded-lg h-full">

                            <!-- Player Header -->
                            <div
                                class="bg-white md:w-1/3 self-center dark:bg-gray-800 p-3 rounded-lg flex items-center border border-transparent hover:border-yellow-500 dark:hover:border-yellow-400 shadow-sm transition-all duration-200 ease-in-out mt-4">
                                <img id="detail-player-image"
                                    class="w-20 h-20 rounded-full border-2 border-yellow-500 mr-4"
                                    src="{{ asset('storage/players/' . $players->first()->image) }}" alt="">
                                <div class="flex flex-col flex-grow min-w-0">
                                    <div class="relative inline-block max-w-full">
                                        <span id="detail-player-name"
                                            class="font-semibold">{{ $players->first()->full_name }}</span>
                                        <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200">
                                        </div>
                                    </div>
                                    <span id="detail-player-original-team"
                                        class="text-xs text-gray-600 mt-1">{{ $players->first()->original_team }}</span>
                                </div>

                                <div class="h-full w-0.5 bg-yellow-500 mx-4 self-center"></div>
                                <div class="flex flex-col justify-center h-full pt-3">
                                    <img id="detail-player-element-icon"
                                        src="{{ asset('storage/icons/elements/' . strtolower($players->first()->element) . '.webp') }}"
                                        class="w-5 h-5 object-contain self-end" onerror="this.style.display='none'">
                                    <img id="detail-player-position-icon"
                                        src="{{ asset('storage/icons/positions/' . strtolower($players->first()->position) . '.webp') }}"
                                        class="w-10 h-10 object-contain self-center"
                                        onerror="this.style.display='none'">
                                </div>
                            </div>


                            <!-- Tabs -->
                            <div class="flex justify-center space-x-2 my-6">
                                <button
                                    class="tab-button bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md"
                                    data-tab="stats">
                                    Estadísticas
                                </button>
                                <button class="tab-button bg-yellow-500 text-white px-4 py-2 rounded-md"
                                    data-tab="techniques">
                                    Técnicas
                                </button>
                            </div>

                            <!-- Stats Content -->
                            <div id="stats-content" class="tab-content overflow-y-auto hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach (['ie1', 'ie2', 'ie3'] as $version)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                            <h3 class="text-lg font-semibold mb-3 text-center dark:text-yellow-400">
                                                {{ strtoupper($version) }}</h3>
                                            <div class="space-y-2">
                                                @foreach ($statLabels as $stat => $label)
                                                    <div
                                                        class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-600">
                                                        <span
                                                            class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                                        <span
                                                            class="text-gray-800 dark:text-yellow-400 font-medium player-stat"
                                                            data-version="{{ $version }}"
                                                            data-stat="{{ $stat }}">--</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Techniques Content -->
                            <div id="techniques-content" class="tab-content overflow-y-auto">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach (['anime1', 'anime2', 'anime3', 'ie1', 'ie2', 'ie3'] as $source)
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 flex flex-col">
                                            <button class="collapse-toggle flex items-center justify-between w-full"
                                                data-target="techniques-{{ $source }}">
                                                <h3
                                                    class="text-lg font-semibold dark:text-yellow-400 text-center w-full">
                                                    @if (str_starts_with($source, 'anime'))
                                                        Temporada Anime {{ substr($source, 5) }}
                                                    @else
                                                        Inazuma Eleven {{ substr($source, 2) }}
                                                    @endif
                                                </h3>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="icon-toggle w-6 h-6 ml-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            </button>

                                            <div id="techniques-{{ $source }}" class="space-y-3 p-2 mt-2">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Player List -->
                    <div class="w-full md:w-1/5 flex flex-col h-full min-h-0 gap-4">
                        <div
                            class="w-full flex flex-col bg-gray-100 dark:bg-gray-700 p-4 rounded-lg h-full overflow-y-auto">
                            <div
                                class="flex justify-between items-end mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-bold dark:text-primary-500">Jugadores</h2>
                                <button onclick="history.back()"
                                    class="flex items-center justify-center gap-2 py-2 px-4 rounded-md border border-black dark:border-primary-500 
                                                    text-black dark:text-primary-500 hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10 
                                                    dark:hover:border-yellow-500 dark:hover:text-yellow-500 transition-all duration-200 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>

                                </button>
                            </div>
                            <div id="player-list" class="overflow-y-auto flex flex-col gap-2">
                                @foreach ($players as $player)
                                    <div id="player-{{ $player->id }}" data-player-id="{{ $player->id }}"
                                        class="list-player bg-white dark:bg-gray-800 p-3 rounded-lg flex items-center cursor-pointer border border-transparent hover:border-yellow-500 dark:hover:border-yellow-400 shadow-sm transition-all duration-200 ease-in-out @if ($loop->first) border-yellow-500 dark:border-yellow-400 @endif">
                                        <img class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 mr-4"
                                            src="{{ asset('storage/players/' . $player->image) }}"
                                            alt="{{ $player->name }}">
                                        <div class="flex flex-col flex-grow min-w-0">
                                            <div class="relative inline-block max-w-full">
                                                <span class="font-semibold">{{ $player->name }}</span>
                                                <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200">
                                                </div>
                                            </div>
                                            <span
                                                class="text-xs text-gray-600 mt-1">{{ $player->original_team }}</span>
                                        </div>
                                        <div class="h-full w-0.5 bg-yellow-500 mx-4 self-center"></div>
                                        <div class="flex flex-col justify-center h-full pt-3">
                                            <img src="{{ asset('storage/icons/elements/' . strtolower($player->element) . '.webp') }}"
                                                class="w-5 h-5 object-contain self-end"
                                                onerror="this.style.display='none'">
                                            <img src="{{ asset('storage/icons/positions/' . strtolower($player->position) . '.webp') }}"
                                                class="w-10 h-10 object-contain self-center"
                                                onerror="this.style.display='none'">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Team Emblem and Coach -->
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">

                            <div
                                class="flex justify-between items-center pb-1 border-b border-gray-200 dark:border-gray-600">
                                <h2 class="text-xl font-bold dark:text-primary-500">
                                    Equipo</h2>
                                <h2>{{ $team->name }}</h2>
                            </div>

                            <div class="flex items-center justify-center space-x-4 mt-4">
                                <img src="{{ asset('storage/coaches/' . $team->coach->image) }}"
                                    alt="{{ $team->coach->name }}" class="w-20 h-20 object-contain">
                                <img src="{{ asset('storage/emblems/' . $team->emblem->image) }}"
                                    alt="{{ $team->emblem->name }}" class="w-20 h-20 object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
