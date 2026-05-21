<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ui-action">
            Secure Portal
        </p>

        <h1 class="mt-2 text-2xl font-bold tracking-tight text-ui-anchor">
            Welcome back
        </h1>

        <p class="mt-1 text-sm text-ui-subtext">
            Sign in to manage assistance requests, claims, settlements, and audit visibility.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="mb-1 flex items-center justify-between gap-3">
                <x-input-label for="password" :value="__('Password')" />

                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-ui-action transition hover:text-primary-dark" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-ui-border text-ui-action shadow-sm focus:ring-ui-action/20" name="remember">
                <span class="ms-2 text-sm text-ui-subtext">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div>
            <x-primary-button class="w-full justify-center rounded-xl bg-ui-action px-5 py-3 text-sm shadow-lg shadow-ui-anchor/15 hover:bg-primary-dark focus:bg-primary-dark focus:ring-ui-action/20">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 border-t border-ui-border/70 pt-5 text-center text-sm text-ui-subtext">
        Need access?
        <a href="{{ route('register') }}" class="font-semibold text-ui-action transition hover:text-primary-dark">
            Create an account
        </a>
    </div>
</x-guest-layout>
