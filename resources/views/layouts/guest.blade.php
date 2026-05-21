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
        <div class="flex min-h-screen flex-col items-center justify-center bg-ui-canvas px-4 py-8">
            <div class="mb-7 text-center">
                <a href="/" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-ui-action text-white shadow-lg shadow-ui-anchor/15 ring-1 ring-ui-action/20">
                        <x-application-logo class="h-7 w-7 fill-current" />
                    </span>

                    <span class="text-xl font-bold tracking-tight text-ui-anchor">
                        EduNexUs
                    </span>
                </a>

                <p class="mt-2 text-sm text-ui-subtext">
                    Cooperative assistance operations platform
                </p>
            </div>

            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface px-6 py-6 shadow-[0_18px_44px_rgba(15,47,44,0.09)] ring-1 ring-white/80 sm:px-8 sm:py-7">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
