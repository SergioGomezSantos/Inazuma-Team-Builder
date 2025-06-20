@php
$mode = isset($team) ? ($mode ?? 'edit') : 'create';
$isViewMode = $mode === 'show';
@endphp

<x-app-layout>
    @section('scripts')
    <script>
        @isset($team)
            window.savedTeamPlayers = @json($team->players ?? []);
            window.savedTeamName = @json($team->name ?? '');
            window.savedTeamId = @json($team->id ?? null);
            window.isEdit = @json(isset($team) ? ($mode ?? 'edit') : 'create');
        @else
            window.savedTeamPlayers = [];
            window.savedTeamName = '';
            window.savedTeamId = null;
        @endisset
    </script>
    @vite(['resources/js/app.js'])
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Main Team Builder Container -->
                <div class="flex flex-col md:flex-row gap-6 h-[calc(100vh-12rem)]">

                    <!-- Left Panel - Team Configuration -->
                    <div class="w-full md:w-1/5 flex flex-col h-full min-h-0 gap-4">
                        <div class="w-full flex flex-col bg-gray-100 dark:bg-gray-700 p-4 rounded-lg h-full
                            overflow-y-auto">
                            <h2 class="text-xl font-bold mb-4 dark:text-primary-500">Equipo</h2>

                            <!-- Team Name Input -->
                            <div class="mb-4">
                                <label for="team-name" class="font-semibold mb-1 block">Nombre</label>
                                <input id="team-name" type="text" value="{{ $team->name ?? '' }}" class=" w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800
                                    shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                    placeholder="Nombre del Equipo" @if($isViewMode) readonly @endif>
                            </div>

                            <!-- Emblem Selection -->
                            <div class="mb-4">
                                <label for="emblem-select" class="font-semibold mb-1 block">Escudo</label>
                                <select id="emblem-select"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                    @if($isViewMode) disabled @endif>
                                    @foreach($emblems as $emblem)
                                    <option value="{{ $emblem->id }}" data-image="{{ $emblem->image }}" @if(isset($team)
                                        && $team->emblem_id == $emblem->id) selected @endif>
                                        {{ $emblem->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="flex justify-center mt-1">
                                    <img id="emblem-image" ¡
                                        src="{{ asset('storage/emblems/'.($currentEmblem->image ?? $emblems->first()->image)) }}"
                                        alt="Emblem" class="w-28 h-28 object-contain">
                                </div>
                            </div>

                            <!-- Coach and Formation Selection -->
                            <div class="mt-auto space-y-4">
                                <!-- Coach Selection -->
                                <div>
                                    <div class="relative h-28 mb-1">
                                        <label for="coach-select"
                                            class="font-semibold absolute left-0 bottom-0">Entrenador</label>
                                        <div class="absolute inset-0 flex justify-center items-center">
                                            <img id="coach-image"
                                                src="{{ asset('storage/coaches/'.($currentCoach->image ?? $coaches->first()->image)) }}"
                                                alt="Coach" class="w-28 h-28 object-contain">
                                        </div>
                                    </div>
                                    <select id="coach-select"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                        @if($isViewMode) disabled @endif>
                                        @foreach($coaches as $coach)
                                        <option value="{{ $coach->id }}" data-image="{{ $coach->image }}"
                                            @if(isset($team) && $team->coach_id == $coach->id) selected @endif>
                                            {{ $coach->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Formation Selection -->
                                <div>
                                    <label for="formation-select" class="font-semibold mb-1 block">Formación</label>
                                    <select id="formation-select"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                        @if($isViewMode) disabled @endif>
                                        @foreach($formations as $formation)
                                        <option value="{{ $formation->id }}" @if(isset($team) && $team->formation_id ==
                                            $formation->id) selected @endif>
                                            {{ $formation->layout }} - {{ $formation->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bench Panel -->
                        <div
                            class="bg-gray-100 dark:bg-gray-700 p-4 rounded-xl shadow-md flex flex-col relative min-h-[220px]">
                            <h2
                                class="text-xl font-bold text-gray-800 dark:text-primary-400 absolute top-2 left-4 z-10">
                                Bench
                            </h2>
                            <div class="flex-1 flex flex-col justify-center items-center gap-3">
                                <!-- Top Row (2 players) -->
                                <div class="flex justify-center gap-4">
                                    <div data-position-id="bench-0"
                                        class="bench-position relative w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20">
                                    </div>
                                    <div data-position-id="bench-1"
                                        class="bench-position relative w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20">
                                    </div>
                                </div>

                                <!-- Bottom Row (3 players) -->
                                <div class="flex justify-center gap-4 mb-6">
                                    <div data-position-id="bench-2"
                                        class="bench-position relative w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20">
                                    </div>
                                    <div data-position-id="bench-3"
                                        class="bench-position relative w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20">
                                    </div>
                                    <div data-position-id="bench-4"
                                        class="bench-position relative w-20 h-20 rounded-full flex items-center justify-center cursor-pointer z-20">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center Panel - Soccer Field -->
                    <div class="w-full md:w-3/5 flex flex-col h-full">
                        <div
                            class="relative overflow-hidden h-full min-h-0 border-2 dark:border-white border-gray-300 rounded-lg">
                            <!-- Soccer field with markings -->
                            <div class="soccer-field absolute inset-0"
                                style="background: repeating-linear-gradient(180deg, #1a6b3a 0px, #1a6b3a 80px, #0d5a2c 80px, #0d5a2c 160px);">

                                <!-- Field markings -->
                                <div class="absolute top-1/2 left-0 right-0 h-px bg-white"></div>
                                <div
                                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 border-2 border-white rounded-full w-24 h-24">
                                </div>
                                <div
                                    class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/2 h-1/4 border-l-2 border-r-2 border-b-2 border-white rounded-b-lg">
                                </div>
                                <div
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1/2 h-1/4 border-l-2 border-r-2 border-t-2 border-white rounded-t-lg">
                                </div>
                                <div
                                    class="absolute top-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1/6 border-l-2 border-r-2 border-b-2 border-white">
                                </div>
                                <div
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1/3 h-1/6 border-l-2 border-r-2 border-t-2 border-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Players and Actions -->
                    <div class="w-full md:w-1/5 flex flex-col h-full min-h-0 gap-4">

                        <!-- Players List -->
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg flex-1 min-h-0 flex flex-col">
                            <h2 class="text-xl font-bold mb-4 dark:text-primary-500">Jugadores</h2>

                            <!-- Search Bar -->
                            <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
                                <input type="text" id="player-search" placeholder="Name / Team / Element / Position ..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>

                            <!-- Players List Container -->
                            <div class="flex-1 overflow-y-auto" id="players-list-container">
                                @foreach($players as $player)
                                <div id="player-{{ $player->id }}" data-player-id="{{ $player->id }}" class="list-player bg-white dark:bg-gray-800 p-3 rounded-lg flex items-center cursor-pointer 
                                    border border-transparent hover:border-yellow-500 dark:hover:border-yellow-400 
                                    shadow-sm transition-all duration-200 ease-in-out" draggable="true"
                                    data-name="{{ strtolower($player->name) }}"
                                    data-fullname="{{ strtolower($player->full_name) }}"
                                    data-team="{{ strtolower($player->original_team) }}"
                                    data-position="{{ strtolower($player->position) }}"
                                    data-element="{{ strtolower($player->element) }}">

                                    <!-- Player Image -->
                                    <div class="relative">
                                        <img class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 mr-4"
                                            src="{{ asset('storage/players/placeholder.png') }}"
                                            data-src="{{ asset('storage/players/' . $player->image) }}"
                                            alt="{{ $player->name }}" loading="lazy">
                                    </div>

                                    <!-- Player Info -->
                                    <div class="flex flex-col flex-grow min-w-0">
                                        <div class="relative inline-block max-w-full">
                                            <span class="font-semibold">{{ $player->name }}</span>
                                            <span class="text-xs font-medium invisible absolute">{{
                                                $player->original_team }}</span>
                                            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200">
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-1">{{ $player->original_team }}</span>
                                    </div>

                                    <!-- Divider -->
                                    <div class="h-full w-0.5 bg-yellow-500 mx-4 self-center"></div>

                                    <!-- Icons -->
                                    <div class="flex flex-col justify-center h-full pt-3">
                                        <div class="tooltip self-end" data-tip="{{ strtoupper($player->element) }}">
                                            <img src="{{ asset('storage/icons/elements/' . strtolower($player->element) . '.webp') }}"
                                                alt="{{ $player->element }}" class="w-5 h-5 object-contain"
                                                onerror="this.style.display='none'">
                                        </div>

                                        <div class="tooltip self-center" data-tip="{{ strtoupper($player->position) }}">
                                            <img src="{{ asset('storage/icons/positions/' . strtolower($player->position) . '.webp') }}"
                                                alt="{{ $player->position }}" class="w-10 h-10 object-contain"
                                                onerror="this.style.display='none'">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Options Block -->
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h2
                                class="text-xl font-bold mb-4 dark:text-primary-500 mb-3 pb-1 border-b border-gray-200 dark:border-gray-600">
                                Opciones</h2>

                            <!-- Names + Design -->
                            <div class="grid grid-cols-2 gap-3 mb-3">

                                <!-- Botón Names -->
                                <button id="toggle-names"
                                    class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md border border-black dark:border-primary-500 text-black dark:text-primary-500 hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10 dark:hover:border-yellow-500 dark:hover:text-yellow-500 transition-all duration-200">
                                    <!-- Icono Visible (Ojo Abierto) -->
                                    <svg id="show-names-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>

                                    <!-- Icono Oculto (Ojo Tachado) -->
                                    <svg id="hide-names-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>

                                    <span class="text-sm">Nombres</span>
                                </button>

                                <!-- Botón Design -->
                                <button id="toggle-design"
                                    class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md border border-black dark:border-primary-500 text-black dark:text-primary-500 hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10 dark:hover:border-yellow-500 dark:hover:text-yellow-500 transition-all duration-200">
                                    <!-- Icono Visible (Círculo Completo) -->
                                    <svg id="show-design-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 12m-10 0a10 10 0 1 0 20 0a10 10 0 1 0 -20 0" />
                                    </svg>

                                    <!-- Icono Oculto (Círculo Tachado) -->
                                    <svg id="hide-design-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 12m-10 0a10 10 0 1 0 20 0a10 10 0 1 0 -20 0" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4l16 16" />
                                    </svg>

                                    <span class="text-sm">Diseño</span>
                                </button>
                            </div>

                            <!-- Save + Data -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <!-- Save Button -->
                                <button id="save-team" @if(!auth()->check() || $isViewMode) disabled @endif
                                    class="w-full font-bold py-2 px-4 rounded-md ease-in-out flex items-center
                                    justify-center gap-2 text-center text-black
                                    @if(auth()->check() && !$isViewMode)
                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500 dark:hover:bg-yellow-500
                                    active:bg-yellow-500 active:dark:bg-yellow-500
                                    @else
                                    bg-gray-300 dark:bg-gray-600 cursor-not-allowed
                                    @endif
                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                    dark:focus:ring-offset-gray-800">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    <span>
                                        @if ($isViewMode || $mode === 'edit')
                                        Actualizar
                                        @else
                                        Guardar
                                        @endif
                                    </span>
                                </button>

                                <!-- Team Data Button -->
                                <button id="data-team" @if(!auth()->check()) disabled @endif
                                    class="w-full font-bold py-2 px-4 text-black rounded-md ease-in-out
                                    @auth bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500
                                    dark:hover:bg-yellow-500 active:bg-yellow-500 active:dark:bg-yellow-500
                                    @else bg-gray-300 cursor-not-allowed dark:bg-gray-600
                                    @endauth
                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                    dark:focus:ring-offset-gray-800
                                    flex items-center justify-center gap-2 text-center">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Datos</span>
                                </button>
                            </div>

                            <!-- Clear + Random -->
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Clear Button -->
                                <button id="clear-team" @if($isViewMode) disabled @endif class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md 
                                    border text-black
                                    @if(!$isViewMode)
                                    dark:border-primary-500 dark:text-primary-500
                                    hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10
                                    dark:hover:border-yellow-500 dark:hover:text-yellow-500
                                    @else
                                    border-none bg-gray-300 dark:bg-gray-600 cursor-not-allowed
                                    @endif
                                    transition-all duration-200" title="Clear">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-sm">Limpiar</span>
                                </button>

                                <!-- Random Button -->
                                <button id="random-team" @if($isViewMode) disabled @endif class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md 
                                    border text-black
                                    @if(!$isViewMode)
                                    dark:border-primary-500 dark:text-primary-500
                                    hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10
                                    dark:hover:border-yellow-500 dark:hover:text-yellow-500
                                    @else
                                    border-none bg-gray-300 dark:bg-gray-600 cursor-not-allowed
                                    @endif
                                    transition-all duration-200" title="Random">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 3h5v5M4 20L21 3m0 13v5h-5m-1-6l6 6M4 4l5 5" />
                                    </svg>
                                    <span class="text-sm">Aleatorio</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                <div class="fixed left-1/2 transform -translate-x-1/2 top-44 flex items-center justify-center"
                    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-4">
                    <div
                        class="bg-red-500 dark:bg-red-800 px-4 py-3 rounded-md shadow-lg flex items-center justify-between max-w-sm">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="ml-4 hover:text-gray-200">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>