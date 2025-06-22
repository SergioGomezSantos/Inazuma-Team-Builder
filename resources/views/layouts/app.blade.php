<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">

<head>
    <script>
        // Establecer modo oscuro por defecto
        if (!localStorage.getItem('color-theme')) {
            localStorage.setItem('color-theme', 'dark');
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark:bg-gray-900');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inazuma Team Builder</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('scripts')
</head>

<body class="h-full font-sans antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-full">
        <!-- Navbar -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm" x-data="{ open: false }">
            <div class="w-full px-8 xl:px-16 2xl:px-32">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center group">
                            <svg class="h-8 w-10 -rotate-[10deg] group-hover:rotate-[10deg] transition-transform duration-300"
                                viewBox="0 0 30 24">
                                <!-- Rayo Inazuma -->
                                <path fill="#FCCD19" stroke="#EE8100" stroke-width="1"
                                    d="M15 2L6 11H11L4 22L18 12H13L24 2H15Z" />
                            </svg>
                            <span class="ml-2 text-xl font-bold relative">
                                <span class="group relative inline-block">
                                    <span class="text-gray-900 dark:text-gray-300">Inazuma Team Builder</span>
                                    <span class="absolute inset-0 overflow-hidden">
                                        <span
                                            class="absolute top-0 left-0 h-full w-0 
                        bg-clip-text text-transparent whitespace-nowrap
                        bg-[linear-gradient(135deg,#fccd19_40%,#ee8100_80%)]
                        group-hover:w-full
                        group-hover:transition-all group-hover:duration-300
                        transition-none">
                                            Inazuma Team Builder
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex items-center space-x-4">

                        <a href="{{ route('teams.story') }}"
                            class="{{ Route::is('teams.story') ? 'text-primary-500' : 'text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500' }} px-3 py-2 text-sm font-medium">
                            Modo Historia
                        </a>

                        @auth
                            <!-- Separador visual -->
                            <span class="h-6 w-px bg-gray-300 dark:bg-gray-600"></span>

                            <a href="{{ route('teams.index') }}"
                                class="{{ Route::is('teams.index') ? 'text-primary-500' : 'text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500' }} px-3 py-2 text-sm font-medium">
                                Tus Equipos
                            </a>

                            <!-- Separador visual -->
                            <span class="h-6 w-px bg-gray-300 dark:bg-gray-600"></span>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 px-3 py-2 text-sm font-medium">
                                    Salir
                                </button>
                            </form>
                        @else
                            <!-- Separador visual -->
                            <span class="h-6 w-px bg-gray-300 dark:bg-gray-600"></span>

                            <a href="{{ route('login') }}"
                                class="text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 px-3 py-2 text-sm font-medium">
                                Iniciar Sesión
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 px-3 py-2 text-sm font-medium">
                                    Registrarse
                                </a>
                            @endif
                        @endauth

                        <!-- Dark Mode Toggle -->
                        <button id="theme-toggle" type="button" class="p-2 group">
                            <!-- Icono de luna (modo oscuro) -->
                            <svg id="theme-toggle-dark-icon"
                                class="hidden w-6 h-6 text-gray-300 group-hover:text-primary-500 transition-colors duration-200"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>

                            <!-- Icono de sol (modo claro) -->
                            <svg id="theme-toggle-light-icon"
                                class="hidden w-6 h-6 text-gray-900 group-hover:text-primary-500 transition-colors duration-200"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="open = !open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="sm:hidden" x-show="open" @click.away="open = false"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="pt-2 pb-4 space-y-1 px-4 bg-white dark:bg-gray-800 shadow-md">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700">Register</a>
                        @endif
                    @endauth

                    <!-- Dark Mode Toggle for Mobile -->
                    <div class="px-3 py-2">
                        <button id="mobile-theme-toggle" type="button"
                            class="flex items-center w-full text-left text-base font-medium text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500">
                            <!-- Icono de luna (modo oscuro) -->
                            <svg id="mobile-theme-toggle-dark-icon" class="hidden w-5 h-5 text-primary-500"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <!-- Icono de sol (modo claro) -->
                            <svg id="mobile-theme-toggle-light-icon" class="hidden w-5 h-5 text-primary-500"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="pt-12">
            <div class="w-full px-4 sm:px-6 mx-auto">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-t-lg">
                    <div class="p-4">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tema para desktop
            const themeToggle = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            // Tema para móvil
            const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
            const mobileThemeToggleDarkIcon = document.getElementById('mobile-theme-toggle-dark-icon');
            const mobileThemeToggleLightIcon = document.getElementById('mobile-theme-toggle-light-icon');

            // Función para aplicar el tema correcto al cargar la página
            function applyInitialTheme() {
                const storedTheme = localStorage.getItem('color-theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                // Si no hay tema guardado, usar el del sistema (o dark por defecto)
                if (storedTheme === null) {
                    localStorage.setItem('color-theme', systemPrefersDark ? 'dark' : 'light');
                }

                // Aplicar el tema guardado (o el del sistema)
                const currentTheme = localStorage.getItem('color-theme') || (systemPrefersDark ? 'dark' : 'light');
                if (currentTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                // Actualizar iconos
                updateThemeIcons();
            }

            // Función para actualizar los iconos según el tema actual
            function updateThemeIcons() {
                const isDark = document.documentElement.classList.contains('dark');

                // Iconos desktop
                themeToggleDarkIcon.classList.toggle('hidden', !isDark);
                themeToggleLightIcon.classList.toggle('hidden', isDark);

                // Iconos móvil
                mobileThemeToggleDarkIcon.classList.toggle('hidden', !isDark);
                mobileThemeToggleLightIcon.classList.toggle('hidden', isDark);
            }

            // Función para alternar el tema
            function toggleTheme() {
                const isDark = document.documentElement.classList.contains('dark');

                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }

                updateThemeIcons();
            }

            // Inicializar tema al cargar
            applyInitialTheme();

            // Event listeners
            themeToggle.addEventListener('click', toggleTheme);
            mobileThemeToggle.addEventListener('click', toggleTheme);
        });
    </script>
</body>

</html>
