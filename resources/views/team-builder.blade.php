<x-app-layout>
    @section('scripts')
    @vite(['resources/js/app.js'])
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Main Team Builder Container -->
                <div class="flex flex-col md:flex-row gap-6 h-[calc(100vh-12rem)]">

                    <!-- Left Panel - Team Configuration -->
                    <div
                        class="w-full md:w-1/5 flex flex-col bg-gray-100 dark:bg-gray-700 p-4 rounded-lg h-full overflow-y-auto">
                        <h2 class="text-xl font-bold mb-4 dark:text-primary-500">Team</h2>

                        <!-- Team Name Input -->
                        <div class="mb-4">
                            <label class="font-semibold mb-1 block">Name</label>
                            <input id="team-name" type="text"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter team name">
                        </div>

                        <!-- Emblem Selection -->
                        <div class="mb-4">
                            <label class="font-semibold mb-1 block">Emblem</label>
                            <select id="emblem-select"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($emblems as $emblem)
                                <option value="{{ $emblem->id }}" data-image="{{ $emblem->image }}">
                                    {{ $emblem->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="flex justify-center h-28 mt-1">
                                <img id="emblem-image" src="{{ asset('storage/emblems/'.$emblems->first()->image) }}"
                                    alt="Emblem" class="w-28 h-28 object-contain">
                            </div>
                        </div>

                        <!-- Coach and Formation Selection -->
                        <div class="mt-auto space-y-4">
                            <!-- Coach Selection -->
                            <div>
                                <div class="relative h-28 mb-1">
                                    <label class="font-semibold absolute left-0 bottom-0">Coach</label>
                                    <div class="absolute inset-0 flex justify-center items-center">
                                        <img id="coach-image"
                                            src="{{ asset('storage/coaches/'.$coaches->first()->image) }}" alt="Coach"
                                            class="w-28 h-28 object-contain">
                                    </div>
                                </div>
                                <select id="coach-select"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" data-image="{{ $coach->image }}">
                                        {{ $coach->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Formation Selection -->
                            <div>
                                <label class="font-semibold mb-1 block">Formation</label>
                                <select id="formation-select"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($formations as $formation)
                                    <option value="{{ $formation->id }}">
                                        {{ $formation->layout }} - {{ $formation->name }}
                                    </option>
                                    @endforeach
                                </select>
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
                            <h2 class="text-xl font-bold mb-4 dark:text-primary-500">Players</h2>

                            <!-- Search Bar -->
                            <div class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
                                <input type="text" id="player-search" placeholder="Search by Name or Team"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <h2 class="text-xl font-bold mb-4 dark:text-primary-500">Options</h2>

                            <!-- Sava + Data -->
                            <div class="grid grid-cols-2 gap-3 mt-3">
                                <!-- Save Button -->
                                <a href="" class="w-full font-bold py-2 px-4 text-black rounded-md ease-in-out 
                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500 dark:hover:bg-yellow-500 
                                    active:bg-yellow-500 active:dark:bg-yellow-500 
                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                    flex items-center justify-center gap-2 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    <span>Save</span>
                                </a>

                                <!-- Team Data Button -->
                                <a href="" class="w-full font-bold py-2 px-4 text-black rounded-md ease-in-out 
                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500 dark:hover:bg-yellow-500 
                                    active:bg-yellow-500 active:dark:bg-yellow-500 
                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                    flex items-center justify-center gap-2 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Team Data</span>
                                </a>
                            </div>

                            <!-- Clear + Random -->
                            <div class="grid grid-cols-2 gap-3 mt-3">
                                <!-- Clear Button -->
                                <button id="clear-team" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md 
                                    border border-black dark:border-primary-500 text-black dark:text-primary-500
                                    hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10
                                    dark:hover:border-yellow-500 dark:hover:text-yellow-500
                                    transition-all duration-200" title="Clear">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="text-sm">Clear</span>
                                </button>

                                <!-- Random Button -->
                                <button id="random-team" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-md 
                                    border border-black dark:border-primary-500 text-black dark:text-primary-500
                                    hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10
                                    dark:hover:border-yellow-500 dark:hover:text-yellow-500
                                    transition-all duration-200" title="Random">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 3h5v5M4 20L21 3m0 13v5h-5m-1-6l6 6M4 4l5 5" />
                                    </svg>
                                    <span class="text-sm">Random</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>