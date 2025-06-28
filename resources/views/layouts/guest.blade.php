<!-- resources/views/layouts/guest.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>@yield('title', 'Inazuma Team Builder')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center group">
                        <svg class="h-16 w-20 -rotate-[10deg] group-hover:rotate-[10deg] transition-transform duration-300"
                            viewBox="0 0 30 24">
                            <!-- Rayo Inazuma -->
                            <path fill="#FCCD19" stroke="#EE8100" stroke-width="1"
                                d="M15 2L6 11H11L4 22L18 12H13L24 2H15Z" />
                        </svg>
                        <span class="ml-0 text-3xl font-bold relative">
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
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', !isDark);
                    localStorage.setItem('color-theme', isDark ? 'light' : 'dark');
                });
            }
        });
    </script>
</body>

</html>
