<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-ui-shell">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'EduNexUs') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="m-0 min-h-screen overflow-x-hidden font-sans antialiased bg-ui-shell text-ui-text">

@php
    $successMessage = session('success');
    $unreadNotificationCount = auth()->user()->unreadNotifications->count();
    $toastMessages = collect([
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('info'),
    ])->filter();
@endphp

@if($toastMessages->isNotEmpty())
    <div id="toast-stack"
         class="pointer-events-none fixed right-4 top-4 z-[100] flex w-[calc(100%-2rem)] flex-col gap-3 sm:right-6 sm:top-6 sm:w-auto">
        @foreach($toastMessages as $type => $message)
            <x-toast :type="$type" :message="$message" />
        @endforeach
    </div>
@endif

@if($successMessage)
    <div id="success-flash"
         data-success-flash
         class="pointer-events-none fixed inset-0 z-[130] flex items-center justify-center px-4 py-6">
        <div data-success-flash-panel
             class="w-full max-w-md translate-y-3 scale-[0.98] rounded-3xl border border-emerald-100 bg-ui-surface/95 p-6 text-center opacity-0 shadow-2xl shadow-ui-anchor/20 ring-1 ring-white/80 backdrop-blur-xl transition duration-200 ease-out">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-ui-success/10 text-ui-success ring-1 ring-ui-success/15">
                <x-icon name="check-circle" size="h-7 w-7" />
            </div>

            <p class="mt-4 text-sm font-semibold uppercase tracking-[0.14em] text-ui-success">
                Success
            </p>

            <p class="mt-2 text-base font-semibold leading-6 text-ui-text">
                {{ $successMessage }}
            </p>
        </div>
    </div>
@endif

@if($unreadNotificationCount > 0)
    <div data-notification-nudge
         data-notification-count="{{ $unreadNotificationCount }}"
         data-notification-user="{{ auth()->id() }}"
         class="pointer-events-none fixed right-4 top-[5.25rem] z-[95] flex max-w-[calc(100vw-2rem)] translate-y-2 items-center gap-3 rounded-2xl border border-ui-border bg-ui-surface/95 px-4 py-3 text-sm font-semibold text-ui-text opacity-0 shadow-xl shadow-ui-anchor/10 ring-1 ring-white/70 backdrop-blur-xl transition duration-300 ease-out sm:right-6">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
            <x-icon name="bell" size="h-4 w-4" />
        </span>

        <span>
            You have {{ $unreadNotificationCount }} new {{ \Illuminate\Support\Str::plural('notification', $unreadNotificationCount) }}.
        </span>
    </div>
@endif

<div x-data="{ mobileSidebarOpen: false }"
     @keydown.escape.window="mobileSidebarOpen = false"
     class="flex min-h-screen w-full max-w-full overflow-x-hidden bg-ui-shell">

    <!-- Mobile Sidebar Backdrop -->
    <div x-cloak
         x-show="mobileSidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-[80] bg-ui-anchor/35 backdrop-blur-sm lg:hidden"></div>

    <!-- Mobile Sidebar Drawer -->
    <aside x-cloak
           data-dashboard-sidebar
           x-show="mobileSidebarOpen"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full opacity-80"
           x-transition:enter-end="translate-x-0 opacity-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0 opacity-100"
           x-transition:leave-end="-translate-x-full opacity-80"
           class="fixed inset-y-0 left-0 z-[90] flex w-[min(84vw,20rem)] flex-col bg-ui-action shadow-2xl shadow-ui-anchor/25 ring-1 ring-white/10 lg:hidden">

        <div class="flex h-[72px] items-center justify-between border-b border-ui-border/70 px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0.5 shadow-sm ring-1 ring-white/20">
                    <x-application-logo class="h-full w-full scale-[1.22]" />
                </span>

                <div class="min-w-0">
                    <h1 class="truncate text-xl font-bold text-ui-anchor">
                        EduNexUs
                    </h1>

                    <p class="mt-1 truncate text-xs text-ui-subtext/90">
                        Assistance and Settlement Infrastructure
                    </p>
                </div>
            </div>

            <button type="button"
                    @click="mobileSidebarOpen = false"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-ui-anchor/10 bg-ui-surface/80 text-ui-anchor shadow-sm shadow-ui-anchor/5 transition hover:bg-ui-surface"
                    aria-label="Close navigation">
                <x-icon name="x" size="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-8 overflow-y-auto px-4 py-6">

            {{-- ADMIN --}}
            @if(auth()->user()->role === 'admin')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Main
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.activity-logs.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="receipt" size="h-5 w-5 shrink-0" />
                            <span>Activity Timeline</span>
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.reports.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="chart" size="h-5 w-5 shrink-0" />
                            <span>Audit Reports</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Assistance
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.assistance-programs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-programs.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="lifebuoy" size="h-5 w-5 shrink-0" />
                            <span>Programs</span>
                        </a>

                        <a href="{{ route('admin.merchant-categories.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.merchant-categories.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="list-checks" size="h-5 w-5 shrink-0" />
                            <span>Categories</span>
                        </a>

                        <a href="{{ route('admin.assistance-requests.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-requests.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="file-text" size="h-5 w-5 shrink-0" />
                            <span>Requests</span>
                        </a>

                        <a href="{{ route('admin.merchants.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.merchants.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="store" size="h-5 w-5 shrink-0" />
                            <span>Merchants</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Financial
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.settlements.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.settlements.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Settlements</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Blockchain
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.blockchain-transactions.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.blockchain-transactions.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="link" size="h-5 w-5 shrink-0" />
                            <span>Verification Logs</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MEMBER --}}
            @if(auth()->user()->role === 'member')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Member Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('member.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('member.assistance-requests.create') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.assistance-requests.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="pen-line" size="h-5 w-5 shrink-0" />
                            <span>Request Assistance</span>
                        </a>

                        <a href="{{ route('member.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.claims.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="ticket" size="h-5 w-5 shrink-0" />
                            <span>My Claims</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MERCHANT --}}
            @if(auth()->user()->role === 'merchant')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Merchant Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('merchant.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('merchant.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.claims.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="check-circle" size="h-5 w-5 shrink-0" />
                            <span>Validate Claim</span>
                        </a>

                        <a href="{{ route('merchant.settlements.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.settlements.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Settlements</span>
                        </a>

                        <a href="{{ route('merchant.payout-settings.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.payout-settings.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Payout Settings</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- AUDITOR --}}
            @if(auth()->user()->role === 'auditor')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Auditor Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('auditor.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('auditor.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

            @endif

        </nav>
    </aside>

    <!-- Sidebar -->
    <aside data-dashboard-sidebar class="hidden lg:flex lg:flex-col lg:w-72 bg-ui-action border-r border-white/10 shadow-[14px_0_38px_rgba(6,78,59,0.22)] backdrop-blur-xl">

        <!-- Logo -->
        <div class="h-[68px] flex items-center px-6 border-b border-ui-border/70">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0.5 shadow-sm ring-1 ring-white/20">
                    <x-application-logo class="h-full w-full scale-[1.22]" />
                </span>

                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-bold text-ui-anchor">
                        EduNexUs
                    </h1>

                    <p class="mt-1 truncate text-xs text-ui-subtext/90">
                        Programmable Assistance and Settlement
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto">

            {{-- ADMIN --}}
            @if(auth()->user()->role === 'admin')

                <!-- Main -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Main
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.activity-logs.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="receipt" size="h-5 w-5 shrink-0" />
                            <span>Activity Timeline</span>
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.reports.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="chart" size="h-5 w-5 shrink-0" />
                            <span>Audit Reports</span>
                        </a>
                    </div>
                </div>

                <!-- Assistance -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Assistance
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.assistance-programs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-programs.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="lifebuoy" size="h-5 w-5 shrink-0" />
                            <span>Programs</span>
                        </a>

                        <a href="{{ route('admin.merchant-categories.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.merchant-categories.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="list-checks" size="h-5 w-5 shrink-0" />
                            <span>Categories</span>
                        </a>

                        <a href="{{ route('admin.assistance-requests.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-requests.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="file-text" size="h-5 w-5 shrink-0" />
                            <span>Requests</span>
                        </a>

                        <a href="{{ route('admin.merchants.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.merchants.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="store" size="h-5 w-5 shrink-0" />
                            <span>Merchants</span>
                        </a>
                    </div>
                </div>

                <!-- Financial -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Financial
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.settlements.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.settlements.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Settlements</span>
                        </a>
                    </div>
                </div>

                <!-- Blockchain -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Blockchain
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.blockchain-transactions.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.blockchain-transactions.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="link" size="h-5 w-5 shrink-0" />
                            <span>Verification Logs</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MEMBER --}}
            @if(auth()->user()->role === 'member')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Member Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('member.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('member.assistance-requests.create') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.assistance-requests.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="pen-line" size="h-5 w-5 shrink-0" />
                            <span>Request Assistance</span>
                        </a>

                        <a href="{{ route('member.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.claims.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="ticket" size="h-5 w-5 shrink-0" />
                            <span>My Claims</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MERCHANT --}}
            @if(auth()->user()->role === 'merchant')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Merchant Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('merchant.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('merchant.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.claims.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="check-circle" size="h-5 w-5 shrink-0" />
                            <span>Validate Claim</span>
                        </a>

                        <a href="{{ route('merchant.settlements.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.settlements.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Settlements</span>
                        </a>

                        <a href="{{ route('merchant.payout-settings.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.payout-settings.*')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="credit-card" size="h-5 w-5 shrink-0" />
                            <span>Payout Settings</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- AUDITOR --}}
            @if(auth()->user()->role === 'auditor')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-[0.14em] text-ui-anchor/45 uppercase">
                        Auditor Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('auditor.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('auditor.dashboard')
                                ? 'bg-ui-action/10 text-ui-anchor shadow-sm ring-1 ring-ui-action/15'
                                : 'text-ui-anchor/80 hover:bg-ui-muted/70 hover:text-ui-anchor' }}">
                            <x-icon name="layout-dashboard" size="h-5 w-5 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

            @endif

        </nav>

    </aside>

    <!-- Main Content -->
    <div class="flex min-w-0 flex-1 flex-col bg-ui-canvas">

        <!-- Top Navbar -->
        <header data-dashboard-topbar class="h-[68px] min-w-0 bg-ui-shell border-b border-ui-anchor/10 shadow-[0_10px_28px_rgba(15,47,44,0.06)] backdrop-blur-xl px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button"
                        @click="mobileSidebarOpen = true"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-ui-anchor/10 bg-ui-surface/85 text-ui-anchor shadow-sm shadow-ui-anchor/5 transition hover:bg-ui-surface lg:hidden"
                        aria-label="Open navigation">
                    <svg class="h-5 w-5"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M4 6h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 18h16"></path>
                    </svg>
                </button>

                <div class="min-w-0">
                    <h2 class="truncate text-lg font-semibold text-ui-anchor sm:text-xl">
                        @yield('title', 'Dashboard')
                    </h2>

                    <p class="mt-1 hidden truncate text-sm text-ui-anchor/70 sm:block">
                        {{ ucfirst(auth()->user()->role) }} Portal &middot; EduNexUs
                    </p>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex shrink-0 items-center gap-2 sm:gap-4">

                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative flex items-center justify-center w-11 h-11 rounded-xl border border-ui-border/80 bg-ui-surface/85 shadow-sm shadow-ui-anchor/5 hover:bg-ui-surface transition">

                        <x-icon name="bell" size="h-5 w-5 text-ui-subtext" />

                        @if($unreadNotificationCount > 0)
                            <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-ui-action text-white text-xs flex items-center justify-center font-semibold">
                                {{ $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <div x-cloak
                         x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="fixed left-4 right-4 top-[4.75rem] z-50 max-h-[calc(100vh-6rem)] overflow-hidden rounded-2xl border border-ui-border bg-ui-surface/95 shadow-xl shadow-ui-anchor/10 backdrop-blur-xl sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-3 sm:w-[min(24rem,calc(100vw-2rem))]">

                        <div class="border-b border-ui-border/70 px-4 py-4 sm:px-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-ui-text">
                                        Notifications
                                    </p>

                                    <p class="text-sm text-ui-subtext/90 mt-1">
                                        Recent EduNexUs updates
                                    </p>

                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                        <a href="{{ route('notifications.index') }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-ui-action hover:text-primary-dark">
                                            View All Notifications
                                            <x-icon name="chevron-right" size="h-3.5 w-3.5" />
                                        </a>

                                        @if($unreadNotificationCount > 0)
                                            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                                @csrf

                                                <button type="submit"
                                                        class="text-xs font-semibold text-ui-subtext transition hover:text-ui-action">
                                                    Mark all as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                @if($unreadNotificationCount > 0)
                                    <span class="shrink-0 rounded-full bg-ui-action/10 px-2 py-1 text-xs font-semibold text-ui-action ring-1 ring-ui-action/15">
                                        {{ $unreadNotificationCount }} New
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="max-h-[min(420px,calc(100vh-14rem))] divide-y divide-ui-border/70 overflow-y-auto">
                            @forelse(auth()->user()->notifications->take(5) as $notification)
                                <div class="px-4 py-4 transition hover:bg-ui-canvas sm:px-5">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 h-3 w-3 shrink-0 rounded-full
                                            {{ is_null($notification->read_at)
                                                ? 'bg-ui-action/10'
                                                : 'bg-ui-muted' }}">
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-ui-text text-sm">
                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                    </p>

                                                    <p class="text-sm text-ui-subtext/90 mt-1">
                                                        {{ $notification->data['message'] ?? 'No details available.' }}
                                                    </p>
                                                </div>

                                                @if(isset($notification->data['status']))
                                                    <x-status-badge :status="$notification->data['status']" size="xs" class="shrink-0" />
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between mt-3">
                                                <p class="text-xs text-ui-subtext/70">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>

                                                @if(isset($notification->data['action_url']))
                                                    <a href="{{ route('notifications.read', $notification->id) }}"
                                                       class="text-xs font-semibold text-ui-action hover:text-primary-dark">
                                                        View
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-10 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-ui-muted text-ui-subtext">
                                        <x-icon name="bell" size="h-6 w-6" />
                                    </div>

                                    <p class="text-ui-subtext/70 text-sm">
                                        No notifications yet.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative min-w-0" x-data="{ userMenuOpen: false }" @keydown.escape.window="userMenuOpen = false">
                    <button type="button"
                            id="user-menu-button"
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex max-w-[13rem] items-center gap-2 bg-ui-surface/85 rounded-xl border border-ui-border/80 px-2 py-2 shadow-sm shadow-ui-anchor/5 hover:bg-ui-surface transition sm:max-w-[17rem] sm:gap-3 sm:px-4 lg:max-w-[20rem]">

                        <div class="w-10 h-10 shrink-0 rounded-full bg-ui-action/10 flex items-center justify-center text-ui-action font-semibold ring-1 ring-ui-action/15">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden min-w-0 sm:block text-left">
                            <p class="truncate text-sm font-semibold text-ui-text" title="{{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </p>

                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-ui-success"></span>

                                <p class="truncate text-xs text-ui-subtext">
                                    {{ ucfirst(auth()->user()->role) }}
                                </p>
                            </div>
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div id="user-dropdown"
                         x-cloak
                         x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-180"
                         x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.98]"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-120"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.98]"
                         class="absolute right-0 mt-3 w-72 max-w-[calc(100vw-2rem)] origin-top-right overflow-hidden rounded-2xl border border-ui-border bg-ui-surface/95 shadow-xl shadow-ui-anchor/10 backdrop-blur-xl z-50">

                        <div class="px-5 py-4 border-b border-ui-border/70">
                            <p class="truncate font-semibold text-ui-text" title="{{ auth()->user()->name }}">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="mt-1 break-all text-sm text-ui-subtext/90">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <div class="py-2">
                            <form method="POST"
                                  action="{{ route('logout') }}"
                                  data-confirm
                                  data-confirm-title="Log out of EduNexUs?"
                                  data-confirm-message="You will be signed out of the current portal session."
                                  data-confirm-button="Log out"
                                  data-confirm-tone="danger"
                                  data-loading-text="Logging out..."
                                  data-loader-title="Logging out..."
                                  data-loader-message="Closing your EduNexUs portal session securely.">
                                @csrf

                                <button type="submit"
                                        class="w-full text-left px-5 py-3 text-sm text-ui-danger hover:bg-ui-danger/5 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Page Content -->
        <main class="w-full min-w-0 flex-1 overflow-x-hidden bg-ui-canvas px-4 pt-4 pb-8 sm:px-6 lg:px-8">
            @yield('content')
        </main>

    </div>

</div>

<div id="confirmation-modal"
     class="fixed inset-0 z-[120] hidden items-center justify-center px-4 py-6"
     aria-labelledby="confirmation-modal-title"
     role="dialog"
     aria-modal="true">
    <div id="confirmation-modal-backdrop"
         class="absolute inset-0 bg-ui-text/55 backdrop-blur-[2px]"></div>

    <div class="relative w-full max-w-md translate-y-3 scale-[0.98] rounded-2xl border border-ui-border bg-ui-surface p-6 opacity-0 shadow-2xl shadow-ui-text/25 transition duration-200 ease-out"
         data-confirm-panel>
        <div class="flex items-start gap-4">
            <div id="confirmation-modal-icon"
                 class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-ui-muted text-sm font-bold text-ui-action ring-1 ring-inset ring-ui-action/15">
                <x-icon name="circle-help" size="h-6 w-6" />
            </div>

            <div class="min-w-0 flex-1">
                <h3 id="confirmation-modal-title"
                    class="text-xl font-semibold tracking-tight text-ui-text">
                    Confirm action
                </h3>

                <p id="confirmation-modal-message"
                   class="mt-2.5 text-sm leading-6 text-ui-subtext">
                    Please confirm before continuing.
                </p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button"
                    id="confirmation-modal-cancel"
                    class="inline-flex items-center justify-center rounded-xl border border-ui-border bg-ui-surface px-5 py-2.5 text-sm font-semibold text-ui-text/85 transition hover:bg-ui-canvas">
                Cancel
            </button>

            <button type="button"
                    id="confirmation-modal-confirm"
                    class="inline-flex items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark">
                <span id="confirmation-modal-spinner"
                      class="mr-2 hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>

                <span id="confirmation-modal-confirm-label">
                    Confirm
                </span>
            </button>
        </div>
    </div>
</div>

<div id="global-loader"
     class="fixed inset-0 z-[140] hidden items-center justify-center px-4 py-6"
     role="status"
     aria-live="polite"
     aria-labelledby="global-loader-title">
    <div class="absolute inset-0 bg-ui-text/60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-sm rounded-3xl border border-ui-border bg-ui-surface p-7 text-center shadow-2xl shadow-ui-text/25">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-ui-canvas ring-1 ring-ui-border/70">
            <div class="relative h-14 w-14">
                <div class="absolute inset-0 rounded-full border-4 border-ui-border"></div>
                <div class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-t-ui-action border-r-ui-proof"></div>
                <div class="absolute inset-3 rounded-full bg-ui-surface shadow-inner"></div>
                <div class="absolute inset-[1.15rem] rounded-full bg-ui-muted"></div>
            </div>
        </div>

        <h3 id="global-loader-title"
            class="mt-6 text-xl font-semibold tracking-tight text-ui-text">
            Processing request...
        </h3>

        <p id="global-loader-message"
           class="mt-2 text-sm leading-6 text-ui-subtext">
            Please keep this window open while EduNexUs completes the operation.
        </p>
    </div>
</div>

<script>
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const closeButton = toast.querySelector('[data-toast-close]');
        let dismissTimer;

        const showToast = () => {
            toast.classList.remove('translate-y-2', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        };

        const dismissToast = () => {
            toast.classList.add('translate-y-2', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');

            window.setTimeout(() => {
                toast.remove();
            }, 300);
        };

        window.setTimeout(showToast, 75);

        dismissTimer = window.setTimeout(dismissToast, 4500);

        closeButton?.addEventListener('click', () => {
            window.clearTimeout(dismissTimer);
            dismissToast();
        });
    });

    const successFlash = document.querySelector('[data-success-flash]');
    const successFlashPanel = successFlash?.querySelector('[data-success-flash-panel]');

    if (successFlash && successFlashPanel) {
        const showSuccessFlash = () => {
            successFlashPanel.classList.remove('translate-y-3', 'scale-[0.98]', 'opacity-0');
            successFlashPanel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
        };

        const dismissSuccessFlash = () => {
            successFlashPanel.classList.add('translate-y-3', 'scale-[0.98]', 'opacity-0');
            successFlashPanel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');

            window.setTimeout(() => {
                successFlash.remove();
            }, 220);
        };

        window.setTimeout(showSuccessFlash, 90);
        window.setTimeout(dismissSuccessFlash, 2800);
    }

    const notificationNudge = document.querySelector('[data-notification-nudge]');

    if (notificationNudge) {
        const notificationCount = notificationNudge.dataset.notificationCount || '0';
        const notificationUser = notificationNudge.dataset.notificationUser || 'guest';
        const storageKey = `edunexus:notification-nudge:${notificationUser}`;
        let lastSeenCount = null;

        try {
            lastSeenCount = window.sessionStorage.getItem(storageKey);
        } catch (error) {
            lastSeenCount = null;
        }

        const showNotificationNudge = () => {
            notificationNudge.classList.remove('translate-y-2', 'opacity-0');
            notificationNudge.classList.add('translate-y-0', 'opacity-100');
        };

        const dismissNotificationNudge = () => {
            notificationNudge.classList.add('translate-y-2', 'opacity-0');
            notificationNudge.classList.remove('translate-y-0', 'opacity-100');

            window.setTimeout(() => {
                notificationNudge.remove();
            }, 320);
        };

        if (lastSeenCount !== notificationCount) {
            try {
                window.sessionStorage.setItem(storageKey, notificationCount);
            } catch (error) {
                // The nudge can still show if browser storage is unavailable.
            }
            window.setTimeout(showNotificationNudge, 250);
            window.setTimeout(dismissNotificationNudge, 4200);
        } else {
            notificationNudge.remove();
        }
    }

    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmationPanel = confirmationModal?.querySelector('[data-confirm-panel]');
    const confirmationBackdrop = document.getElementById('confirmation-modal-backdrop');
    const confirmationTitle = document.getElementById('confirmation-modal-title');
    const confirmationMessage = document.getElementById('confirmation-modal-message');
    const confirmationIcon = document.getElementById('confirmation-modal-icon');
    const confirmationCancel = document.getElementById('confirmation-modal-cancel');
    const confirmationConfirm = document.getElementById('confirmation-modal-confirm');
    const confirmationConfirmLabel = document.getElementById('confirmation-modal-confirm-label');
    const confirmationSpinner = document.getElementById('confirmation-modal-spinner');
    const globalLoader = document.getElementById('global-loader');
    const globalLoaderTitle = document.getElementById('global-loader-title');
    const globalLoaderMessage = document.getElementById('global-loader-message');

    let pendingConfirmationForm = null;

    const toneStyles = {
        default: {
            icon: 'bg-ui-muted text-ui-action ring-ui-action/15',
            button: 'bg-ui-action hover:bg-primary-dark focus:ring-ui-action/20',
            symbol: 'circle-help',
        },
        success: {
            icon: 'bg-ui-success/10 text-ui-success ring-ui-success/15',
            button: 'bg-ui-success hover:bg-ui-success/90 focus:ring-ui-success/20',
            symbol: 'check-circle',
        },
        danger: {
            icon: 'bg-ui-danger/10 text-ui-danger ring-ui-danger/15',
            button: 'bg-ui-danger hover:bg-ui-danger/90 focus:ring-ui-danger/20',
            symbol: 'x-circle',
        },
        warning: {
            icon: 'bg-ui-warning/10 text-ui-warning ring-ui-warning/15',
            button: 'bg-ui-warning hover:bg-ui-warning/90 focus:ring-ui-warning/20',
            symbol: 'alert-triangle',
        },
    };

    const confirmationIconSvg = {
        'alert-triangle': '<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>',
        'check-circle': '<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>',
        'circle-help': '<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4"></path><path d="M12 17h.01"></path></svg>',
        'x-circle': '<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>',
    };

    const setConfirmationLoading = (isLoading, label = null) => {
        if (!confirmationConfirm || !confirmationCancel || !confirmationConfirmLabel || !confirmationSpinner) {
            return;
        }

        confirmationConfirm.disabled = isLoading;
        confirmationCancel.disabled = isLoading;

        confirmationConfirm.classList.toggle('cursor-wait', isLoading);
        confirmationConfirm.classList.toggle('opacity-85', isLoading);
        confirmationCancel.classList.toggle('cursor-not-allowed', isLoading);
        confirmationCancel.classList.toggle('opacity-60', isLoading);
        confirmationSpinner.classList.toggle('hidden', !isLoading);

        if (label) {
            confirmationConfirmLabel.textContent = label;
        }
    };

    const setTone = (tone) => {
        const selectedTone = toneStyles[tone] ?? toneStyles.default;

        confirmationIcon.className = `flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ring-1 ring-inset ${selectedTone.icon}`;
        confirmationIcon.innerHTML = confirmationIconSvg[selectedTone.symbol] ?? confirmationIconSvg['circle-help'];
        confirmationConfirm.className = `inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition focus:outline-none focus:ring-4 ${selectedTone.button}`;
    };

    const showGlobalLoader = (form) => {
        if (!globalLoader || !globalLoaderTitle || !globalLoaderMessage) {
            return;
        }

        globalLoaderTitle.textContent = form.dataset.loaderTitle
            || form.dataset.loadingText
            || 'Processing request...';
        globalLoaderMessage.textContent = form.dataset.loaderMessage
            || 'Please keep this window open while EduNexUs completes the operation.';

        globalLoader.classList.remove('hidden');
        globalLoader.classList.add('flex');
    };

    const closeConfirmationModal = () => {
        if (!confirmationModal || !confirmationPanel) {
            return;
        }

        setConfirmationLoading(false);

        confirmationPanel.classList.add('translate-y-3', 'scale-[0.98]', 'opacity-0');
        confirmationPanel.classList.remove('translate-y-0', 'scale-100', 'opacity-100');

        window.setTimeout(() => {
            confirmationModal.classList.add('hidden');
            confirmationModal.classList.remove('flex');
            pendingConfirmationForm = null;
        }, 180);
    };

    const openConfirmationModal = (form) => {
        if (!confirmationModal || !confirmationPanel) {
            return;
        }

        pendingConfirmationForm = form;

        confirmationTitle.textContent = form.dataset.confirmTitle || 'Confirm action';
        confirmationMessage.textContent = form.dataset.confirmMessage || 'Please confirm before continuing.';
        confirmationConfirmLabel.textContent = form.dataset.confirmButton || 'Confirm';
        setTone(form.dataset.confirmTone || 'default');
        setConfirmationLoading(false);

        confirmationModal.classList.remove('hidden');
        confirmationModal.classList.add('flex');

        window.setTimeout(() => {
            confirmationPanel.classList.remove('translate-y-3', 'scale-[0.98]', 'opacity-0');
            confirmationPanel.classList.add('translate-y-0', 'scale-100', 'opacity-100');
        }, 25);
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.matches('form[data-confirm]')) {
            return;
        }

        if (form.dataset.confirmBypass === 'true') {
            return;
        }

        event.preventDefault();
        openConfirmationModal(form);
    });

    confirmationCancel?.addEventListener('click', closeConfirmationModal);
    confirmationBackdrop?.addEventListener('click', closeConfirmationModal);

    confirmationConfirm?.addEventListener('click', () => {
        if (!pendingConfirmationForm) {
            closeConfirmationModal();
            return;
        }

        const submitButton = pendingConfirmationForm.querySelector('button[type="submit"]');
        const loadingText = pendingConfirmationForm.dataset.loadingText;

        setConfirmationLoading(true, loadingText || 'Submitting...');

        if (submitButton) {
            submitButton.disabled = true;

            if (loadingText) {
                submitButton.textContent = loadingText;
            }
        }

        pendingConfirmationForm.dataset.confirmBypass = 'true';
        showGlobalLoader(pendingConfirmationForm);
        pendingConfirmationForm.submit();
    });

    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && confirmationModal && !confirmationModal.classList.contains('hidden')) {
            closeConfirmationModal();
        }
    });
</script>

</body>
</html>
