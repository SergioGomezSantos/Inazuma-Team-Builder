<x-app-layout>
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const thead = document.querySelector('thead tr');

                new Sortable(thead, {
                    animation: 150,
                    handle: 'th',
                    filter: '.ignore-sort',
                    onMove: function(evt) {
                        const draggedCol = evt.dragged.getAttribute('data-col');
                        const targetCol = evt.related.getAttribute('data-col');
                        return targetCol !== 'data' && draggedCol !== 'data';
                    },
                    onEnd: function() {
                        const headers = Array.from(thead.children);
                        const tbody = document.querySelector('tbody');

                        Array.from(tbody.children).forEach(row => {
                            const newCellsOrder = headers.map(header => {
                                const colName = header.getAttribute('data-col');
                                return row.querySelector(`[data-col="${colName}"]`);
                            }).filter(cell => cell !== null);

                            while (row.firstChild) {
                                row.removeChild(row.firstChild);
                            }
                            newCellsOrder.forEach(cell => {
                                row.appendChild(cell);
                            });
                        });
                    }
                });

                // Estado inicial: Solo Técnicas
                let toggleView = 'techniques';
                updateView();

                // Funcionalidad para alternar entre modos de vista
                document.getElementById('toggleView').addEventListener('click', function() {
                    if (toggleView === 'techniques') {
                        toggleView = 'stats';
                    } else if (toggleView === 'stats') {
                        toggleView = 'both';
                    } else {
                        toggleView = 'techniques';
                    }

                    updateView();
                });

                function updateView() {

                    const statsColumns = document.querySelectorAll('[data-col^="stats-"]');
                    const techniquesColumns = document.querySelectorAll('[data-col^="techniques-"]');

                    const statsIcon = document.getElementById('statsIcon');
                    const techniquesIcon = document.getElementById('techniquesIcon');


                    if (toggleView === 'techniques') {
                        // Mostrar solo técnicas
                        statsColumns.forEach(col => col.style.display = 'none');
                        techniquesColumns.forEach(col => col.style.display = '');

                        statsIcon.classList.remove('hidden');
                        techniquesIcon.classList.add('hidden');

                    } else if (toggleView === 'stats') {
                        // Mostrar solo stats
                        statsColumns.forEach(col => col.style.display = '');
                        techniquesColumns.forEach(col => col.style.display = 'none');

                        statsIcon.classList.remove('hidden');
                        techniquesIcon.classList.remove('hidden');

                    } else {
                        // Mostrar ambos
                        statsColumns.forEach(col => col.style.display = '');
                        techniquesColumns.forEach(col => col.style.display = '');

                        statsIcon.classList.add('hidden');
                        techniquesIcon.classList.remove('hidden');
                    }
                }
            });
        </script>
    @endsection

    <!-- Mensaje flotante de notificación -->
    <div class="fixed left-1/2 transform -translate-x-1/2 top-30 z-50" x-data="{ showReorderNotification: localStorage.getItem('reorderNotificationDismissed') !== 'true' }"
        x-show="showReorderNotification" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4">
        <div class="bg-gray-400 dark:bg-yellow-500 px-4 py-3 rounded-md shadow-lg flex items-center max-w-md">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-white font-bold">Puedes Reordenar las Columnas</span>
            </div>
            <button
                @click="showReorderNotification = false; localStorage.setItem('reorderNotificationDismissed', 'true')"
                class="ml-4 text-white hover:text-gray-200 focus:outline-none">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

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
    @endphp

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Main Team Builder Container -->
                <div class="h-[calc(100vh-12rem)] flex flex-col rounded-lg bg-gray-100 dark:bg-gray-700">

                    <!-- Fixed Header With Button -->
                    <div class="pt-11 pr-12 flex justify-end items-center">
                        <button id="toggleView"
                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-md border border-black dark:border-primary-500 text-black dark:text-primary-500 hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10 dark:hover:border-yellow-500 dark:hover:text-yellow-500 transition-all duration-200">

                            <svg id="statsIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="w-5 h-5" stroke-width="1.5" stroke="currentColor" class="size-6">
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />

                            </svg>

                            <svg id="techniquesIcon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 2L3 14h7l-1 8L21 10h-7l1-8z" />
                            </svg>

                            <span>Cambiar Vista</span>
                        </button>
                    </div>

                    <div
                        class="w-full flex-grow bg-gray-100 dark:bg-gray-700 pt-0 p-6 rounded-lg overflow-hidden flex flex-col">

                        <!-- Table Container -->
                        <div
                            class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600 flex-grow m-6">

                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr class="dark:text-primary-500">
                                        <th scope="col" class="px-0 py-3 text-center min-w-32 ignore-sort"
                                            data-col="data">
                                            Datos</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="stats-ie1">
                                            Stats IE 1</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="techniques-ie1">
                                            Técnicas IE 1</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="stats-ie2">
                                            Stats IE 2</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="techniques-ie2">
                                            Técnicas IE 2</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="stats-ie3">
                                            Stats IE 3</th>
                                        <th scope="col" class="px-0 py-3 text-center" data-col="techniques-ie3">
                                            Técnicas IE 3</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($players as $player)
                                        <tr>
                                            <td class="whitespace-nowrap ignore-sort" data-col="data">

                                                <img class="h-24 w-24 mx-auto"
                                                    src="{{ asset('storage/players/' . $player->image) }}">

                                                <div class="flex justify-center items-center gap-2">
                                                    <img src="{{ asset('storage/icons/positions/' . $player->position) . '.webp' }}"
                                                        class="w-10 h-10 object-contain">
                                                    <img src="{{ asset('storage/icons/elements/' . $player->element) . '.webp' }}"
                                                        class="w-5 h-5 object-contain">
                                                </div>

                                                <div class="text-sm text-center">
                                                    {{ $player->full_name }}
                                                </div>

                                            </td>

                                            <td class="whitespace-nowrap text-right p-12" data-col="stats-ie1">
                                                @php
                                                    $stat = $player->stats->firstWhere('version', 'ie1');
                                                @endphp

                                                @if ($stat)
                                                    @foreach (array_keys($statLabels) as $key)
                                                        <div
                                                            class="flex justify-between items-center mb-2 gap-x-8 border-b-2">
                                                            <span
                                                                class="dark:text-yellow-500">{{ $stat->$key }}</span>
                                                            <span>{{ $statLabels[$key] }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </td>

                                            <td class="px-0 p-4 text-center" data-col="techniques-ie1">
                                                <div class="flex flex-col items-center">
                                                    <ul
                                                        class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'anime1')
                                                                @php
                                                                    $withPlayers = json_decode(
                                                                        $technique->pivot->with ?? '[]',
                                                                        true,
                                                                    );
                                                                    $withPlayers = is_array($withPlayers)
                                                                        ? $withPlayers
                                                                        : [];
                                                                @endphp

                                                                <li class="flex flex-col gap-1">
                                                                    <div class="flex gap-2 items-center">
                                                                        <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                            class="w-7 h-5">
                                                                        <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                            class="w-5 h-5">
                                                                        <span>{{ $technique->name }}</span>
                                                                    </div>

                                                                    @if (count($withPlayers) > 0)
                                                                        @php
                                                                            $firstLine = array_slice(
                                                                                $withPlayers,
                                                                                0,
                                                                                3,
                                                                            );
                                                                            $secondLine = array_slice($withPlayers, 3);
                                                                        @endphp

                                                                        <div class="flex flex-col gap-1 text-sm pl-1">
                                                                            <div class="flex gap-1 items-center">
                                                                                <span class="ml-12">|</span>
                                                                                @foreach ($firstLine as $playerName)
                                                                                    @php
                                                                                        $playerExists =
                                                                                            $players->firstWhere(
                                                                                                'name',
                                                                                                $playerName,
                                                                                            ) !== null;
                                                                                        $textColor = $playerExists
                                                                                            ? 'text-green-500'
                                                                                            : 'text-red-500';
                                                                                    @endphp

                                                                                    <span
                                                                                        class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                    @if (!$loop->last)
                                                                                        <span>,</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>

                                                                            @if (count($secondLine) > 0)
                                                                                <div
                                                                                    class="flex gap-1 items-center ml-12">
                                                                                    <span>|</span>
                                                                                    @foreach ($secondLine as $playerName)
                                                                                        @php
                                                                                            $playerExists =
                                                                                                $players->firstWhere(
                                                                                                    'name',
                                                                                                    $playerName,
                                                                                                ) !== null;
                                                                                            $textColor = $playerExists
                                                                                                ? 'text-green-500'
                                                                                                : 'text-red-500';
                                                                                        @endphp

                                                                                        <span
                                                                                            class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                        @if (!$loop->last)
                                                                                            <span>,</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>

                                                    <ul class="space-y-4 border-l-2 pl-4 my-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'ie1')
                                                                <li class="flex gap-2 items-center">
                                                                    <div class="w-5 h-5">
                                                                        @if (optional($technique)->element)
                                                                            <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                                class="w-full h-full">
                                                                        @endif
                                                                    </div>
                                                                    <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                        class="w-7 h-5">
                                                                    <span>{{ $technique->name }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap text-right p-12" data-col="stats-ie2">
                                                @php
                                                    $stat = $player->stats->firstWhere('version', 'ie2');
                                                @endphp

                                                @if ($stat)
                                                    @foreach (array_keys($statLabels) as $key)
                                                        <div
                                                            class="flex justify-between items-center mb-2 gap-x-8 border-b-2">
                                                            <span
                                                                class="dark:text-yellow-500">{{ $stat->$key }}</span>
                                                            <span>{{ $statLabels[$key] }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </td>

                                            <td class="px-0 p-4 text-center" data-col="techniques-ie2">
                                                <div class="flex flex-col items-center">
                                                    <ul
                                                        class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'anime2')
                                                                @php
                                                                    $withPlayers = json_decode(
                                                                        $technique->pivot->with ?? '[]',
                                                                        true,
                                                                    );
                                                                    $withPlayers = is_array($withPlayers)
                                                                        ? $withPlayers
                                                                        : [];
                                                                @endphp

                                                                <li class="flex flex-col gap-1">
                                                                    <div class="flex gap-2 items-center">
                                                                        <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                            class="w-7 h-5">
                                                                        <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                            class="w-5 h-5">
                                                                        <span>{{ $technique->name }}</span>
                                                                    </div>

                                                                    @if (count($withPlayers) > 0)
                                                                        @php
                                                                            $firstLine = array_slice(
                                                                                $withPlayers,
                                                                                0,
                                                                                3,
                                                                            );
                                                                            $secondLine = array_slice($withPlayers, 3);
                                                                        @endphp

                                                                        <div class="flex flex-col gap-1 text-sm pl-1">
                                                                            <div class="flex gap-1 items-center">
                                                                                <span class="ml-12">|</span>
                                                                                @foreach ($firstLine as $playerName)
                                                                                    @php
                                                                                        $playerExists =
                                                                                            $players->firstWhere(
                                                                                                'name',
                                                                                                $playerName,
                                                                                            ) !== null;
                                                                                        $textColor = $playerExists
                                                                                            ? 'text-green-500'
                                                                                            : 'text-red-500';
                                                                                    @endphp

                                                                                    <span
                                                                                        class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                    @if (!$loop->last)
                                                                                        <span>,</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>

                                                                            @if (count($secondLine) > 0)
                                                                                <div
                                                                                    class="flex gap-1 items-center ml-12">
                                                                                    <span>|</span>
                                                                                    @foreach ($secondLine as $playerName)
                                                                                        @php
                                                                                            $playerExists =
                                                                                                $players->firstWhere(
                                                                                                    'name',
                                                                                                    $playerName,
                                                                                                ) !== null;
                                                                                            $textColor = $playerExists
                                                                                                ? 'text-green-500'
                                                                                                : 'text-red-500';
                                                                                        @endphp

                                                                                        <span
                                                                                            class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                        @if (!$loop->last)
                                                                                            <span>,</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>

                                                    <ul class="space-y-4 border-l-2 pl-4 my-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'ie2')
                                                                <li class="flex gap-2 items-center">
                                                                    <div class="w-5 h-5">
                                                                        @if (optional($technique)->element)
                                                                            <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                                class="w-full h-full">
                                                                        @endif
                                                                    </div>
                                                                    <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                        class="w-7 h-5">
                                                                    <span>{{ $technique->name }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>

                                            <td class="whitespace-nowrap text-right p-12" data-col="stats-ie3">
                                                @php
                                                    $stat = $player->stats->firstWhere('version', 'ie3');
                                                @endphp

                                                @if ($stat)
                                                    @foreach (array_keys($statLabels) as $key)
                                                        <div
                                                            class="flex justify-between items-center mb-2 gap-x-8 border-b-2">
                                                            <span
                                                                class="dark:text-yellow-500">{{ $stat->$key }}</span>
                                                            <span>{{ $statLabels[$key] }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </td>

                                            <td class="px-0 p-4 text-center" data-col="techniques-ie3">
                                                <div class="flex flex-col items-center">
                                                    <ul
                                                        class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'anime3')
                                                                @php
                                                                    $withPlayers = json_decode(
                                                                        $technique->pivot->with ?? '[]',
                                                                        true,
                                                                    );
                                                                    $withPlayers = is_array($withPlayers)
                                                                        ? $withPlayers
                                                                        : [];
                                                                @endphp

                                                                <li class="flex flex-col gap-1">
                                                                    <div class="flex gap-2 items-center">
                                                                        <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                            class="w-7 h-5">
                                                                        <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                            class="w-5 h-5">
                                                                        <span>{{ $technique->name }}</span>
                                                                    </div>

                                                                    @if (count($withPlayers) > 0)
                                                                        @php
                                                                            $firstLine = array_slice(
                                                                                $withPlayers,
                                                                                0,
                                                                                3,
                                                                            );
                                                                            $secondLine = array_slice($withPlayers, 3);
                                                                        @endphp

                                                                        <div class="flex flex-col gap-1 text-sm pl-1">
                                                                            <div class="flex gap-1 items-center">
                                                                                <span class="ml-12">|</span>
                                                                                @foreach ($firstLine as $playerName)
                                                                                    @php
                                                                                        $playerExists =
                                                                                            $players->firstWhere(
                                                                                                'name',
                                                                                                $playerName,
                                                                                            ) !== null;
                                                                                        $textColor = $playerExists
                                                                                            ? 'text-green-500'
                                                                                            : 'text-red-500';
                                                                                    @endphp

                                                                                    <span
                                                                                        class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                    @if (!$loop->last)
                                                                                        <span>,</span>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>

                                                                            @if (count($secondLine) > 0)
                                                                                <div
                                                                                    class="flex gap-1 items-center ml-12">
                                                                                    <span>|</span>
                                                                                    @foreach ($secondLine as $playerName)
                                                                                        @php
                                                                                            $playerExists =
                                                                                                $players->firstWhere(
                                                                                                    'name',
                                                                                                    $playerName,
                                                                                                ) !== null;
                                                                                            $textColor = $playerExists
                                                                                                ? 'text-green-500'
                                                                                                : 'text-red-500';
                                                                                        @endphp

                                                                                        <span
                                                                                            class="{{ $textColor }}">{{ $playerName }}</span>
                                                                                        @if (!$loop->last)
                                                                                            <span>,</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>

                                                    <ul class="space-y-4 border-l-2 pl-4 my-8 w-full max-w-xs">
                                                        @foreach ($player->techniques as $technique)
                                                            @if ($technique->pivot->source == 'ie3')
                                                                <li class="flex gap-2 items-center">
                                                                    <div class="w-5 h-5">
                                                                        @if (optional($technique)->element)
                                                                            <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                                class="w-full h-full">
                                                                        @endif
                                                                    </div>
                                                                    <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                        class="w-7 h-5">
                                                                    <span>{{ $technique->name }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
