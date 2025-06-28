<x-app-layout>
    @section('title', 'Modo Historia')

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggleButton = document.getElementById('toggle-names');
                const teamNames = document.querySelectorAll('.team-name');

                // Estado inicial: nombres visibles
                let namesVisible = true;

                toggleButton.addEventListener('click', () => {
                    namesVisible = !namesVisible;

                    // Alternar iconos
                    document.getElementById('show-names-icon').classList.toggle('hidden', namesVisible);
                    document.getElementById('hide-names-icon').classList.toggle('hidden', !namesVisible);

                    // Alternar visibilidad de nombres
                    teamNames.forEach(name => {
                        name.classList.toggle('opacity-100', !namesVisible);
                    });
                });
            });
        </script>
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Main Team Builder Container -->
                <div class="h-[calc(100vh-12rem)] bg-gray-100 dark:bg-gray-700 relative p-8 flex flex-col rounded-lg">
                    <!-- Fixed Header With Button -->
                    <div class="p-3 pr-4 flex justify-end items-center">
                        <button id="toggle-names"
                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-md border border-black dark:border-primary-500 text-black dark:text-primary-500 hover:border-yellow-500 hover:text-yellow-500 hover:bg-yellow-500 hover:bg-opacity-10 dark:hover:border-yellow-500 dark:hover:text-yellow-500 transition-all duration-200">
                            <svg id="show-names-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg id="hide-names-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>

                            <span class="text-sm">Nombres</span>
                        </button>
                    </div>

                    <!-- Scroll Area -->
                    <div class="flex-1 overflow-y-auto rounded-b-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start justify-center">
                            @php
                                $sections = [
                                    'Principales' => [
                                        'teams' => $mainTeams,
                                        'colSpan' => 'md:col-span-2',
                                    ],
                                    'Fase Clasificatoria Regional' => [
                                        'teams' => $regionalTeams,
                                    ],
                                    'Torneo Fútbol Frontier' => [
                                        'teams' => $futbolFrontierTeams,
                                    ],
                                    'Institutos Temporada 2' => [
                                        'teams' => $institutosAnime2Teams,
                                        'maxWidth' => 'max-w-[calc(5*8rem)]',
                                        'shortNames' => [
                                            'Mary Times Memorial' => 'Mary Times Mem.',
                                            'Royal Academy Redux' => 'Royal A. Redux',
                                        ],
                                    ],
                                    'Academia Alius' => [
                                        'teams' => $aliusTeams,
                                        'shortNames' => [
                                            'Tormenta de Géminis' => 'Torm. de Géminis',
                                            'Emperadores Oscuros' => 'Emp. Oscuros',
                                        ],
                                    ],
                                    'Futbol Frontier Internacional' => [
                                        'teams' => $mundialTeams,
                                        'colSpan' => 'md:col-span-2',
                                        'shortNames' => [
                                            'Dragones de Fuego' => 'Drag. de Fuego',
                                            'Leones del Desierto' => 'Leo. del Desierto',
                                        ],
                                    ],
                                    'Enemigos' => [
                                        'teams' => $enemiesTeams,
                                        'colSpan' => 'md:col-span-2',
                                    ],
                                    'Extras' => [
                                        'teams' => $extrasTeams,
                                        'colSpan' => 'md:col-span-2',
                                        'maxWidth' => 'max-w-[calc(9*8rem)]',
                                        'shortNames' => [
                                            'Caimanes del Cabo' => 'Caim. del Cabo',
                                        ],
                                    ],
                                ];
                            @endphp

                            @foreach ($sections as $title => $section)
                                <div
                                    class="bg-white dark:bg-gray-800 text-center p-4 rounded-lg shadow-md {{ $section['colSpan'] ?? '' }} h-full">
                                    <h2 class="dark:text-primary-500 text-xl font-bold">{{ $title }}</h2>
                                    <div
                                        class="flex flex-wrap items-center justify-center gap-4 {{ $section['maxWidth'] ?? '' }} mx-auto">
                                        @foreach ($section['teams'] as $team)
                                            <div class="w-32 flex flex-col items-center justify-start group">
                                                <a href="{{ route('teams.show', $team->id) }}">
                                                    <div class="w-full h-32 flex items-center justify-center">
                                                        @php
                                                            $size = App\Helpers\EmblemHelper::getEmblemSize(
                                                                $team->name,
                                                            );
                                                        @endphp
                                                        <img class="{{ $size }} transition-transform group-hover:scale-110 duration-200 ease-in-out"
                                                            src="{{ asset('/storage/emblems/' . $team->emblem->image) }}"
                                                            alt="{{ $team->name }}">
                                                    </div>
                                                </a>
                                                <div
                                                    class="team-name text-sm text-gray-700 dark:text-gray-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center mt-2">
                                                    @if (isset($section['shortNames']) && isset($section['shortNames'][$team->name]))
                                                        {{ $section['shortNames'][$team->name] }}
                                                    @else
                                                        {{ $team->name }}
                                                    @endif
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
    </div>
</x-app-layout>
