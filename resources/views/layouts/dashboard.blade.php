<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'EduNexUs') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900">

@php
    $toastMessages = collect([
        'success' => session('success'),
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

<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 bg-white border-r border-slate-200">

        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-100">
            <div>
                <h1 class="text-2xl font-bold text-teal-700">
                    EduNexUs
                </h1>

                <p class="text-xs text-slate-500 mt-1">
                    Programmable Cooperative Assistance
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto">

            {{-- ADMIN --}}
            @if(auth()->user()->role === 'admin')

                <!-- Main -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Main
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.dashboard')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📊</span>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.activity-logs.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>🧾</span>
                            <span>Activity Timeline</span>
                        </a>
                    </div>
                </div>

                <!-- Assistance -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Assistance
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.assistance-programs.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-programs.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>🛟</span>
                            <span>Programs</span>
                        </a>

                        <a href="{{ route('admin.assistance-requests.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.assistance-requests.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📄</span>
                            <span>Requests</span>
                        </a>

                        <a href="{{ route('admin.merchants.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.merchants.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>🏪</span>
                            <span>Merchants</span>
                        </a>
                    </div>
                </div>

                <!-- Financial -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Financial
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.settlements.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.settlements.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>💳</span>
                            <span>Settlements</span>
                        </a>
                    </div>
                </div>

                <!-- Blockchain -->
                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Blockchain
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('admin.blockchain-transactions.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('admin.blockchain-transactions.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>⛓️</span>
                            <span>Verification Logs</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MEMBER --}}
            @if(auth()->user()->role === 'member')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Member Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('member.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.dashboard')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📊</span>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('member.assistance-requests.create') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.assistance-requests.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📝</span>
                            <span>Request Assistance</span>
                        </a>

                        <a href="{{ route('member.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('member.claims.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>🎟️</span>
                            <span>My Claims</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- MERCHANT --}}
            @if(auth()->user()->role === 'merchant')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Merchant Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('merchant.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.dashboard')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📊</span>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('merchant.claims.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('merchant.claims.*')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>✅</span>
                            <span>Validate Claim</span>
                        </a>
                    </div>
                </div>

            @endif

            {{-- AUDITOR --}}
            @if(auth()->user()->role === 'auditor')

                <div>
                    <p class="px-4 mb-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">
                        Auditor Portal
                    </p>

                    <div class="space-y-2">
                        <a href="{{ route('auditor.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition
                           {{ request()->routeIs('auditor.dashboard')
                                ? 'bg-teal-50 text-teal-700'
                                : 'text-slate-600 hover:bg-slate-100' }}">
                            <span>📊</span>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

            @endif

        </nav>

    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">
                    @yield('title', 'Dashboard')
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ ucfirst(auth()->user()->role) }} Portal · EduNexUs
                </p>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">

                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative flex items-center justify-center w-11 h-11 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">

                        <span class="text-xl">🔔</span>

                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-teal-600 text-white text-xs flex items-center justify-center font-semibold">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-3 w-[380px] bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden z-50">

                        <div class="px-5 py-4 border-b border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-800">
                                        Notifications
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        Recent EduNexUs updates
                                    </p>

                                    <div class="mt-3">
                                        <a href="{{ route('notifications.index') }}"
                                           class="inline-flex items-center text-xs font-semibold text-teal-600 hover:text-teal-700">
                                            View All Notifications →
                                        </a>
                                    </div>
                                </div>

                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="px-2 py-1 rounded-full bg-teal-100 text-teal-700 text-xs font-semibold">
                                        {{ auth()->user()->unreadNotifications->count() }} New
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="max-h-[420px] overflow-y-auto divide-y divide-slate-100">
                            @forelse(auth()->user()->notifications->take(8) as $notification)
                                <div class="px-5 py-4 hover:bg-slate-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 w-3 h-3 rounded-full
                                            {{ is_null($notification->read_at)
                                                ? 'bg-teal-500'
                                                : 'bg-slate-300' }}">
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-semibold text-slate-800 text-sm">
                                                        {{ $notification->data['title'] ?? 'Notification' }}
                                                    </p>

                                                    <p class="text-sm text-slate-500 mt-1">
                                                        {{ $notification->data['message'] ?? 'No details available.' }}
                                                    </p>
                                                </div>

                                                @if(isset($notification->data['status']))
                                                    <span class="px-2 py-1 rounded-full text-[11px] font-semibold
                                                        {{ $notification->data['status'] === 'Approved'
                                                            ? 'bg-emerald-100 text-emerald-700'
                                                            : 'bg-slate-100 text-slate-600' }}">
                                                        {{ $notification->data['status'] }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between mt-3">
                                                <p class="text-xs text-slate-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>

                                                @if(isset($notification->data['action_url']))
                                                    <a href="{{ route('notifications.read', $notification->id) }}"
                                                       class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                                                        View
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-10 text-center">
                                    <p class="text-slate-400 text-sm">
                                        No notifications yet.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="relative">
                    <button type="button"
                            id="user-menu-button"
                            class="flex items-center gap-3 bg-white rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50 transition">

                        <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ auth()->user()->name }}
                            </p>

                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                <p class="text-xs text-slate-500">
                                    {{ ucfirst(auth()->user()->role) }}
                                </p>
                            </div>
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div id="user-dropdown"
                         class="hidden absolute right-0 mt-3 w-56 bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden z-50">

                        <div class="px-5 py-4 border-b border-slate-100">
                            <p class="font-semibold text-slate-800">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center px-5 py-3 text-sm text-slate-600 hover:bg-slate-50 transition">
                                Profile Settings
                            </a>

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
                                        class="w-full text-left px-5 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
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
         class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]"></div>

    <div class="relative w-full max-w-md translate-y-3 scale-[0.98] rounded-2xl border border-slate-200 bg-white p-6 opacity-0 shadow-2xl shadow-slate-950/25 transition duration-200 ease-out"
         data-confirm-panel>
        <div class="flex items-start gap-4">
            <div id="confirmation-modal-icon"
                 class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-sm font-bold text-teal-700 ring-1 ring-inset ring-teal-100">
                ?
            </div>

            <div class="min-w-0 flex-1">
                <h3 id="confirmation-modal-title"
                    class="text-xl font-semibold tracking-tight text-slate-900">
                    Confirm action
                </h3>

                <p id="confirmation-modal-message"
                   class="mt-2.5 text-sm leading-6 text-slate-500">
                    Please confirm before continuing.
                </p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button"
                    id="confirmation-modal-cancel"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </button>

            <button type="button"
                    id="confirmation-modal-confirm"
                    class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700">
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
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-7 text-center shadow-2xl shadow-slate-950/25">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-slate-50 ring-1 ring-slate-100">
            <div class="relative h-14 w-14">
                <div class="absolute inset-0 rounded-full border-4 border-teal-100"></div>
                <div class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-t-teal-600 border-r-cyan-500"></div>
                <div class="absolute inset-3 rounded-full bg-white shadow-inner"></div>
                <div class="absolute inset-[1.15rem] rounded-full bg-teal-500"></div>
            </div>
        </div>

        <h3 id="global-loader-title"
            class="mt-6 text-xl font-semibold tracking-tight text-slate-900">
            Processing request...
        </h3>

        <p id="global-loader-message"
           class="mt-2 text-sm leading-6 text-slate-500">
            Please keep this window open while EduNexUs completes the operation.
        </p>
    </div>
</div>

<script>
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    userMenuButton?.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    window.addEventListener('click', function (e) {
        if (
            userMenuButton &&
            userDropdown &&
            !userMenuButton.contains(e.target) &&
            !userDropdown.contains(e.target)
        ) {
            userDropdown.classList.add('hidden');
        }
    });

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
            icon: 'bg-teal-50 text-teal-700 ring-teal-100',
            button: 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-200',
            symbol: '?',
        },
        success: {
            icon: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            button: 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-200',
            symbol: 'OK',
        },
        danger: {
            icon: 'bg-rose-50 text-rose-700 ring-rose-100',
            button: 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-200',
            symbol: '!',
        },
        warning: {
            icon: 'bg-amber-50 text-amber-700 ring-amber-100',
            button: 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-200',
            symbol: '!',
        },
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

        confirmationIcon.className = `flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-sm font-bold ring-1 ring-inset ${selectedTone.icon}`;
        confirmationIcon.textContent = selectedTone.symbol;
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
