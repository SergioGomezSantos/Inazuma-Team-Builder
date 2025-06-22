<x-app-layout>
    @section('scripts')
    @endsection

    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- Main Team Builder Container -->
                <div class="h-[calc(100vh-12rem)] flex flex-col">
                    <div
                        class="w-full flex-grow bg-gray-100 dark:bg-gray-700 p-6 rounded-lg shadow-inner overflow-hidden flex flex-col">

                        <!-- Table Container -->
                        <div class="overflow-auto rounded-lg border border-gray-200 dark:border-gray-600 flex-grow m-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr class="dark:text-primary-500">
                                        <th scope="col" class="px-0 py-3 text-center w-44">Escudo</th>
                                        <th scope="col" class="px-0 py-3 text-center w-44">Entrenador</th>
                                        <th scope="col" class="px-0 py-3 text-left">Formación</th>
                                        <th scope="col" class="pl-4 py-3 text-right">Nombre</th>
                                        <th scope="col" class="px-0 py-3 text-center w-64">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($teams as $team)
                                        <tr>
                                            <td class="px-0 py-4 whitespace-nowrap">
                                                <img class="h-20 w-20 mx-auto"
                                                    src="{{ asset('storage/emblems/' . $team->emblem->image) }}"
                                                    alt="Team emblem">
                                            </td>
                                            <td class="px-0 py-4 whitespace-nowrap">
                                                <img class="h-14 w-14 mx-auto"
                                                    src="{{ asset('storage/coaches/' . $team->coach->image) }}"
                                                    alt="Team emblem">
                                            </td>
                                            <td class="px-0 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    {{ $team->formation->layout }} - {{ $team->formation->name }}
                                                </div>
                                            </td>
                                            <td class="pl-4 py-4 whitespace-nowrap">
                                                <div class="text-md text-right pr-4">
                                                    {{ $team->name }}
                                                </div>
                                            </td>
                                            <td class="px-0 py-4 whitespace-nowrap">
                                                <div class="flex items-center justify-center space-x-2">

                                                    <!-- Show Button -->
                                                    <a href="{{ route('teams.show', $team->id) }}" id="data-team"
                                                        @if (!auth()->check()) disabled @endif
                                                        class="font-bold py-2 px-4 text-black rounded-md ease-in-out
                                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500
                                                    dark:hover:bg-yellow-500 active:bg-yellow-500
                                                    active:dark:bg-yellow-500
                                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                                    dark:focus:ring-offset-gray-800
                                                    flex items-center justify-center gap-2 text-center">
                                                        <svg id="show-names-icon" xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Data Button -->
                                                    <a href="{{ route('teams.players', $team->id) }}" id="data-team"
                                                        @if (!auth()->check()) disabled @endif
                                                        class="font-bold py-2 px-4 text-black rounded-md ease-in-out
                                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500
                                                    dark:hover:bg-yellow-500 active:bg-yellow-500
                                                    active:dark:bg-yellow-500
                                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                                    dark:focus:ring-offset-gray-800
                                                    flex items-center justify-center gap-2 text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                    </a>

                                                    <!-- Builder Button -->
                                                    <a href="{{ route('teams.edit', $team->id) }}" id="save-team"
                                                        class="font-bold py-2 px-4 text-black rounded-md ease-in-out
                                                    bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500
                                                    dark:hover:bg-yellow-500 active:bg-yellow-500 active:dark:bg-yellow-500
                                                    focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                                    dark:focus:ring-offset-gray-800
                                                    flex items-center justify-center gap-2 text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="h-5 w-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M4.867 19.125h.008v.008h-.008v-.008Z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('teams.destroy', $team->id) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="font-bold py-2 px-4 text-white rounded-md ease-in-out
                                                            bg-red-600 hover:bg-red-700 dark:bg-red-600
                                                            dark:hover:bg-red-700 active:bg-red-800 active:dark:bg-red-800
                                                            focus:ring-2 focus:ring-red-500 focus:ring-offset-2
                                                            dark:focus:ring-offset-gray-800
                                                            flex items-center justify-center gap-2 text-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                        @if (session('success'))
                            <div class="fixed left-1/2 transform -translate-x-1/2 top-55 flex items-center justify-center"
                                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-y-4"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform translate-y-4">
                                <div
                                    class="bg-green-500 dark:bg-green-800 px-4 py-3 rounded-md shadow-lg flex items-center justify-between max-w-sm">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ session('success') }}</span>
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
        </div>
    </div>
</x-app-layout>
