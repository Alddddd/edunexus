<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ui-text antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-ui-canvas px-4 py-6 sm:py-8">
            <div class="mb-6 text-center sm:mb-7">
                <a href="/" class="inline-flex items-center gap-3">
                    <span class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white p-0.5 shadow-lg shadow-ui-anchor/10 ring-1 ring-ui-border/80">
                        <x-application-logo class="h-full w-full scale-[1.22]" />
                    </span>

                    <span class="text-xl font-bold tracking-tight text-ui-anchor">
                        EduNexUs
                    </span>
                </a>

                <p class="mt-2 text-sm text-ui-subtext">
                    Programmable assistance and settlement infrastructure
                </p>
            </div>

            <div class="w-full max-w-6xl overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface px-5 py-5 shadow-[0_18px_44px_rgba(15,47,44,0.09)] ring-1 ring-white/80 sm:px-8 sm:py-7 2xl:max-w-[72rem]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
