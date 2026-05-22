<x-guest-layout>
    <div class="mx-auto grid w-full max-w-[68rem] gap-7 lg:grid-cols-[minmax(20rem,0.82fr)_minmax(0,1.18fr)] lg:items-start xl:gap-8">
        <div>
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ui-action">
                    Secure Portal
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-ui-anchor">
                    Welcome back
                </h1>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Sign in to manage programmable assistance, QR claim validation, merchant settlements, and audit-ready proof records.
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

                    <div class="relative" x-data="{ showPassword: false }">
                        <x-text-input id="password" class="block w-full rounded-xl border-ui-border bg-ui-surface py-3 pl-4 pr-14 text-ui-text shadow-sm focus:border-ui-action focus:ring-ui-action/20"
                                      x-bind:type="showPassword ? 'text' : 'password'"
                                      name="password"
                                      required autocomplete="current-password" />

                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-1.5 right-1.5 inline-flex w-11 items-center justify-center rounded-lg border border-ui-border/70 bg-ui-canvas/80 text-ui-action shadow-sm transition hover:bg-ui-muted hover:text-ui-anchor active:scale-95"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                            <span x-show="!showPassword" x-cloak>
                                <x-icon name="key" size="h-4 w-4" />
                            </span>
                            <span x-show="showPassword" x-cloak>
                                <x-icon name="lock" size="h-4 w-4" />
                            </span>
                        </button>
                    </div>

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
        </div>

        <x-demo-portal compact class="lg:min-h-full" />
    </div>
</x-guest-layout>
