<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-blue-50 px-4 py-6 sm:px-6 lg:px-8">
        {{-- Card --}}
        <div class="w-full max-w-md rounded-xl bg-white p-8 shadow-lg">
            {{-- Logo --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('img/logo-pln.png') }}" alt="PLN Logo" class="h-16" />
            </div>

            {{-- Status session (breeze component) --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Judul --}}
            <h1 class="text-2xl font-bold text-center mb-1">{{ __('Masuk') }}</h1>
            <p class="text-sm text-center text-gray-500 mb-6">Masukkan email & password untuk masuk</p>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" placeholder="Masukkan email Anda" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" placeholder="Masukkan password Anda" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Remember Me
                <div class="mb-6 flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
                </div> --}}

                {{-- Button --}}
                <x-primary-button class="w-full justify-center">
                    {{ __('MASUK') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-guest-layout>
