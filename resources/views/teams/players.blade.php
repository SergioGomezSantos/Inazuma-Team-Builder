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

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Main Team Builder Container -->
                <div class="h-[calc(100vh-12rem)] flex flex-col rounded-lg">
                    <div
                        class="w-full flex-grow bg-gray-100 dark:bg-gray-700 p-6 rounded-lg shadow-inner overflow-hidden flex flex-col">

                        <!-- Table Container -->
                        <div
                            class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600 flex-grow m-6">

                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr class="dark:text-primary-500">
                                        <th scope="col" class="px-0 py-3 text-center w-52 ignore-sort"
                                            data-col="data">Datos</th>
                                        <th scope="col" class="px-0 py-3 text-center w-52" data-col="ie1">IE 1</th>
                                        <th scope="col" class="px-0 py-3 text-center w-52" data-col="ie2">IE 2</th>
                                        <th scope="col" class="px-0 py-3 text-center w-52" data-col="ie3">IE 3</th>
                                        <th scope="col" class="px-0 py-3 text-center w-20" data-col="stats">
                                            Estadísticas</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($players as $player)
                                        <tr>
                                            <td class="px-0 py-4 whitespace-nowrap ignore-sort" data-col="data">

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

                                            <td class="px-0 p-4" data-col="ie1">
                                                <ul class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'anime')
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
                                                                    <div class="flex gap-1 items-center text-sm pl-1">
                                                                        <span class="ml-12">|</span>
                                                                        @foreach ($withPlayers as $playerName)
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
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>

                                                <ul class="space-y-4 border-l-2 pl-4 my-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'ie')
                                                            <li class="flex gap-2 items-center">
                                                                <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                    class="w-5 h-5">
                                                                <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                    class="w-7 h-5">
                                                                <span>{{ $technique->name }}</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>

                                            <td class="px-0 p-4" data-col="ie2">
                                                <ul class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'anime')
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
                                                                    <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                        class="w-5 h-5">
                                                                    <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                        class="w-7 h-5">
                                                                    <span>{{ $technique->name }}</span>
                                                                </div>

                                                                @if (count($withPlayers) > 0)
                                                                    <div class="flex gap-1 items-center text-sm pl-1">
                                                                        <span class="ml-12">|</span>
                                                                        @foreach ($withPlayers as $playerName)
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
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>

                                                <ul class="space-y-4 border-l-2 pl-4 my-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'ie')
                                                            <li class="flex gap-2 items-center">
                                                                <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                    class="w-5 h-5">
                                                                <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                    class="w-7 h-5">
                                                                <span>{{ $technique->name }}</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>

                                            <td class="px-0 p-4" data-col="ie3">
                                                <ul class="space-y-4 border-l-2 dark:border-yellow-500 pl-4 mt-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'anime')
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
                                                                    <div class="flex gap-1 items-center text-sm pl-1">
                                                                        <span class="ml-12">|</span>
                                                                        @foreach ($withPlayers as $playerName)
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
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>

                                                <ul class="space-y-4 border-l-2 pl-4 my-8">
                                                    @foreach ($player->techniques as $technique)
                                                        @if ($technique->pivot->source == 'ie')
                                                            <li class="flex gap-2 items-center">
                                                                <img src="{{ asset('storage/icons/elements/' . $technique->element) . '.webp' }}"
                                                                    class="w-5 h-5">
                                                                <img src="{{ asset('storage/icons/types/' . $technique->type) . '.webp' }}"
                                                                    class="w-7 h-5">
                                                                <span>{{ $technique->name }}</span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>

                                            <td class="whitespace-nowrap text-right p-12" data-col="stats">
                                                @foreach ($player->stats as $stat => $value)
                                                    <div
                                                        class="flex justify-between items-center mb-2 space-y-2 gap-x-8 border-b-2">
                                                        <span class="dark:text-yellow-500">{{ $value }}</span>
                                                        <span>{{ $stat }}</span>
                                                    </div>
                                                @endforeach
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
