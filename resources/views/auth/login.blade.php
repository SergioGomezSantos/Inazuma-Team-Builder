<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg"
        :status="session('status')" />

    <div class="mb-8 text-center">
        <p class="text-gray-600 dark:text-gray-400"><b>Login</b> to Save your Teams</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input id="email"
                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition duration-200"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" />
            <x-text-input id="password"
                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition duration-200"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-yellow-500 shadow-sm focus:ring-primary-500 transition duration-200"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500"
                href="{{ route('password.request') }}">
                {{ __('Forgot password?') }}
            </a>
            @endif
        </div>

        <div class="pt-4">
            <button class="w-full font-bold py-2 px-4 text-black rounded-md ease-in-out
                bg-primary-500 hover:bg-yellow-500 dark:bg-primary-500
                dark:hover:bg-yellow-500 active:bg-yellow-500 active:dark:bg-yellow-500
                focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                dark:focus:ring-offset-gray-800">
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
    <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}"
            class="font-medium underline text-gray-900 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-500">
            {{ __('Register here') }}
        </a>
    </div>
    @endif
</x-guest-layout>