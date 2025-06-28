<x-app-layout>
    @section('title', 'Top Jugadores por Estadísticas')

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tab system
                const tabs = document.querySelectorAll('[data-tab]');
                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');

                        // Hide all content
                        document.querySelectorAll('.stats-content').forEach(c => c.classList.add(
                            'hidden'));
                        // Show selected
                        document.getElementById(tabId).classList.remove('hidden');

                        // Update active tab
                        tabs.forEach(t => {
                            t.classList.remove('bg-yellow-500', 'text-black');
                            t.classList.add('bg-gray-200', 'dark:bg-gray-700');
                        });
                        this.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                        this.classList.add('bg-yellow-500', 'text-black');
                    });
                });

                // Activate first tab
                tabs[0].click();

                // Auto-submit form when filters change
                document.querySelectorAll('#filters select').forEach(select => {
                    select.addEventListener('change', function() {
                        document.getElementById('filters').submit();
                    });
                });
            });
        </script>
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Main Container -->
                <div class="h-[calc(100vh-12rem)] flex flex-col rounded-lg bg-gray-100 dark:bg-gray-700 p-12 pt-8">

                    <!-- Filters -->
                    <form id="filters" method="GET" action="{{ route('stats.top-players') }}" class="mb-4 mx-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Version Selector -->
                            <div>
                                <label for="version"
                                    class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Versión</label>
                                <select name="version" id="version"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 dark:bg-gray-800 shadow-sm dark:border-gray-600 dark:text-gray-300">
                                    <option value="all" {{ $selectedVersion == 'all' ? 'selected' : '' }}>Todas
                                    </option>
                                    @foreach ($versions as $version)
                                        <option value="{{ $version }}"
                                            {{ $selectedVersion == $version ? 'selected' : '' }}>
                                            {{ strtoupper($version) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Position Selector -->
                            <div>
                                <label for="position"
                                    class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Posición</label>
                                <select name="position" id="position"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 dark:bg-gray-800 shadow-sm dark:border-gray-600 dark:text-gray-300">
                                    <option value="">Todas</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position }}"
                                            {{ $selectedPosition == $position ? 'selected' : '' }}>
                                            {{ $position }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Element Selector -->
                            <div>
                                <label for="element"
                                    class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Elemento</label>
                                <select name="element" id="element"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 dark:bg-gray-800 shadow-sm dark:border-gray-600 dark:text-gray-300">
                                    <option value="">Todos</option>
                                    @foreach ($elements as $element)
                                        <option value="{{ $element }}"
                                            {{ $selectedElement == $element ? 'selected' : '' }}>
                                            {{ $element }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Stats Navigation -->
                    <div class="flex justify-center overflow-x-auto gap-2 pb-4 mx-4 border-b border-gray-600">
                        @foreach ($statLabels as $stat => $label)
                            <button data-tab="{{ $stat }}"
                                class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Content  -->
                    <div class="flex-1 overflow-y-auto pt-4">
                        @foreach ($statLabels as $stat => $label)
                            <div id="{{ $stat }}" class="stats-content hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($topPlayers[$stat] as $player)
                                        @php
                                            $playerStats = $player->current_stats;
                                            $currentStatValue = $playerStats->{$stat} ?? 0;
                                        @endphp

                                        <!-- Player Card -->
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-transparent hover:border-yellow-500 dark:hover:border-yellow-400 transition-all duration-200 p-4">
                                            <!-- Player Header -->
                                            <div
                                                class="p-3 flex items-center border-b border-gray-200 dark:border-gray-600">
                                                <!-- Player Image -->
                                                <div class="relative">
                                                    <img class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 mr-4"
                                                        src="{{ asset('storage/players/' . $player->image) }}"
                                                        alt="{{ $player->name }}" loading="lazy">
                                                </div>

                                                <!-- Player Info -->
                                                <div class="flex flex-col flex-grow min-w-0">
                                                    <div class="relative inline-block max-w-full">
                                                        <span class="font-semibold">{{ $player->name }}</span>
                                                        <div
                                                            class="absolute bottom-0 left-0 right-0 border-t border-gray-200 dark:border-gray-600">
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span
                                                            class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $player->original_team }}</span>
                                                        @if ($player->current_stats)
                                                            <span
                                                                class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ strtoupper($player->current_stats->version) }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Divider -->
                                                <div class="h-full w-0.5 bg-yellow-500 mx-4 self-center"></div>

                                                <!-- Icons -->
                                                <div class="flex flex-col justify-center h-full pt-3">
                                                    <div class="tooltip self-end"
                                                        data-tip="{{ strtoupper($player->element) }}">
                                                        <img src="{{ asset('storage/icons/elements/' . strtolower($player->element) . '.webp') }}"
                                                            alt="{{ $player->element }}" class="w-5 h-5 object-contain"
                                                            onerror="this.style.display='none'">
                                                    </div>

                                                    <div class="tooltip self-center"
                                                        data-tip="{{ strtoupper($player->position) }}">
                                                        <img src="{{ asset('storage/icons/positions/' . strtolower($player->position) . '.webp') }}"
                                                            alt="{{ $player->position }}"
                                                            class="w-10 h-10 object-contain"
                                                            onerror="this.style.display='none'">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Stats -->
                                            <div class="p-3">
                                                @foreach ($statLabels as $s => $l)
                                                    <div
                                                        class="flex justify-between items-center py-1 {{ $s == $stat ? 'border-b-2 border-yellow-500 dark:border-yellow-400' : 'border-b border-gray-200 dark:border-gray-600' }}">
                                                        <span
                                                            class="{{ $s == $stat ? 'text-base text-yellow-500 dark:text-yellow-400 font-bold' : 'text-sm text-gray-600 dark:text-gray-300' }}">
                                                            {{ $l }}
                                                        </span>
                                                        <span
                                                            class="{{ $s == $stat ? 'text-lg text-yellow-500 dark:text-yellow-400 font-bold' : 'text-gray-600 dark:text-gray-300' }}">
                                                            {{ $playerStats->{$s} ?? 0 }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
