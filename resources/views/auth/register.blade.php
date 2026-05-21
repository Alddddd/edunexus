<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ui-action">
            Cooperative Access
        </p>

        <h1 class="mt-2 text-2xl font-bold tracking-tight text-ui-anchor">
            Create your account
        </h1>

        <p class="mt-1 text-sm text-ui-subtext">
            Register for EduNexUs. Role access is assigned by your cooperative administrator.
        </p>
    </div>

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach(['Member', 'Admin', 'Merchant', 'Auditor'] as $role)
            <span class="rounded-full border border-ui-border bg-ui-canvas px-3 py-1 text-xs font-semibold text-ui-subtext">
                {{ $role }}
            </span>
        @endforeach
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1 block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-ui-border bg-ui-surface px-4 py-3 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="rounded-xl border border-ui-action/15 bg-ui-action/10 px-4 py-3 text-xs leading-5 text-ui-action">
            Your account can be connected to member, admin, merchant, or auditor workflows after verification.
        </div>

        <div>
            <x-primary-button class="w-full justify-center rounded-xl bg-ui-action px-5 py-3 text-sm shadow-lg shadow-ui-anchor/15 hover:bg-primary-dark focus:bg-primary-dark focus:ring-ui-action/20">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 border-t border-ui-border/70 pt-5 text-center text-sm text-ui-subtext">
        Already registered?
        <a class="font-semibold text-ui-action transition hover:text-primary-dark" href="{{ route('login') }}">
            Sign in
        </a>
    </div>
</x-guest-layout>
