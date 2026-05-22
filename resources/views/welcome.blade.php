<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EduNexUs | Cooperative Assistance Infrastructure</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html { scroll-behavior: smooth; }

        @keyframes float-soft {
            0%, 100% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(0, -12px, 0); }
        }

        @keyframes scan-line {
            0%, 100% { top: 8px; opacity: 0; }
            12%, 78% { opacity: 1; }
            88% { top: calc(100% - 10px); opacity: 0; }
        }

        @keyframes proof-pulse {
            0%, 100% { opacity: .55; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.04); }
        }

        @keyframes cursor-submit {
            0%, 10% { opacity: 0; transform: translate3d(54px, -54px, 0) scale(.92); }
            18%, 44% { opacity: 1; transform: translate3d(16px, 8px, 0) scale(1); }
            50%, 56% { opacity: 1; transform: translate3d(12px, 14px, 0) scale(.82); }
            68%, 100% { opacity: 0; transform: translate3d(30px, -16px, 0) scale(.9); }
        }

        @keyframes cursor-approve {
            0%, 12% { opacity: 0; transform: translate3d(48px, -48px, 0) scale(.92); }
            20%, 48% { opacity: 1; transform: translate3d(4px, 64px, 0) scale(1); }
            54%, 60% { opacity: 1; transform: translate3d(2px, 68px, 0) scale(.82); }
            72%, 100% { opacity: 0; transform: translate3d(18px, 30px, 0) scale(.9); }
        }

        @keyframes button-confirm {
            0%, 48% { filter: none; transform: scale(1); }
            54%, 60% { filter: brightness(.9); transform: scale(.97); }
            72%, 100% { filter: none; transform: scale(1); }
        }

        @keyframes audit-row-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes hash-reveal {
            from { max-width: 0; }
            to { max-width: 18ch; }
        }

        @keyframes live-type {
            0%, 12% { max-width: 0; opacity: .65; }
            38%, 78% { max-width: 36ch; opacity: 1; }
            100% { max-width: 36ch; opacity: .82; }
        }

        .landing-grid {
            background-image:
                linear-gradient(to right, rgba(6, 78, 59, .055) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 78, 59, .055) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .hero-stage {
            overflow-x: clip;
            overflow-y: visible;
        }

        .landing-section {
            position: relative;
            overflow: hidden;
        }

        .hero-stage.landing-section {
            overflow-x: clip;
            overflow-y: visible;
        }

        .landing-section-frame {
            min-height: calc(100svh - 5.5rem);
            display: flex;
            align-items: center;
        }

        .landing-section-inner {
            width: 100%;
            max-width: 80rem;
            margin-inline: auto;
            padding-inline: 1rem;
            padding-block: 4rem;
        }

        .hero-safe-frame {
            position: relative;
            isolation: isolate;
            overflow: visible;
            contain: layout;
        }

        .hero-safe-frame::before {
            content: '';
            position: absolute;
            inset: 3rem 0 1rem;
            z-index: -1;
            border-radius: 2rem;
            background: radial-gradient(circle at 50% 45%, rgba(6, 78, 59, .12), transparent 62%);
        }

        .hero-visual {
            min-height: 560px;
        }

        .hero-dashboard-shell {
            position: absolute;
            left: 50%;
            top: 45%;
            width: min(86vw, 450px);
            transform: translate(-50%, -50%);
        }

        .hero-dashboard {
            transform: perspective(1200px) rotateX(5deg) rotateY(-8deg) rotateZ(.5deg);
            transform-style: preserve-3d;
            box-shadow: 0 34px 80px rgba(6, 78, 59, .18), 0 0 0 1px rgba(255, 255, 255, .55);
        }

        .float-a { animation: float-soft 7s ease-in-out infinite; }
        .float-b { animation: float-soft 8s ease-in-out infinite 1s; }
        .float-c { animation: float-soft 9s ease-in-out infinite 1.8s; }
        .qr-scan-line { animation: scan-line 2.4s ease-in-out infinite; }
        .proof-pulse { animation: proof-pulse 2.4s ease-in-out infinite; }
        .cursor-submit { animation: cursor-submit 4.4s ease-in-out infinite; }
        .cursor-approve { animation: cursor-approve 4.6s ease-in-out infinite; }
        .button-confirm { animation: button-confirm 4.4s ease-in-out infinite; }
        .section-anchor { scroll-margin-top: 104px; }
        .presentation-section {
            min-height: calc(100svh - 5.5rem);
            display: flex;
            align-items: center;
        }
        .closing-section {
            min-height: min(680px, calc(100svh - 5.5rem));
            display: flex;
            align-items: center;
        }

        .closing-section > .landing-section-inner {
            padding-block: 4rem;
        }
        .stage-rail {
            background: linear-gradient(90deg, rgba(6, 78, 59, .16), rgba(8, 145, 178, .24), rgba(6, 78, 59, .16));
        }
        .stage-rail::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .75), transparent);
            opacity: .45;
        }

        .hamburger-line {
            display: block;
            height: 2px;
            width: 18px;
            border-radius: 999px;
            background: currentColor;
            transition: transform .22s ease, opacity .22s ease;
        }

        .audit-console .audit-row,
        .audit-console .audit-status {
            opacity: 0;
        }

        .audit-console .audit-hash {
            display: inline-block;
            max-width: 0;
            overflow: hidden;
            white-space: nowrap;
            vertical-align: bottom;
        }

        .audit-console.is-visible .audit-hash {
            animation: hash-reveal 1.2s steps(18, end) .2s forwards;
        }

        .audit-console.is-visible .audit-status {
            animation: audit-row-in .5s ease .55s forwards;
        }

        .audit-console.is-visible .audit-row {
            animation: audit-row-in .5s ease forwards;
        }

        .audit-console.is-visible .audit-row:nth-child(1) { animation-delay: .8s; }
        .audit-console.is-visible .audit-row:nth-child(2) { animation-delay: 1s; }
        .audit-console.is-visible .audit-row:nth-child(3) { animation-delay: 1.2s; }

        .audit-live-message {
            display: inline-block;
            max-width: 0;
            min-height: 1.25rem;
            overflow: hidden;
            white-space: nowrap;
            vertical-align: bottom;
        }

        .audit-console.is-visible .audit-live-message {
            animation: live-type 3s steps(22, end) infinite;
        }

        [data-reveal] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .65s ease, transform .65s ease;
        }

        [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
            .hero-dashboard { transform: none !important; }
            [data-reveal] { opacity: 1 !important; transform: none !important; }
            .audit-console .audit-row,
            .audit-console .audit-status { opacity: 1 !important; }
            .audit-console .audit-hash { max-width: 18ch !important; }
            .audit-live-message { max-width: 22ch !important; }
        }

        @media (max-width: 640px) {
            .audit-status {
                overflow: hidden;
            }

            .audit-live-message {
                max-width: 22ch;
            }
        }

        @media (max-width: 1023px) {
            .landing-section-frame {
                min-height: auto;
                display: block;
            }

            .landing-section-inner {
                padding-inline: 1.5rem;
                padding-block: 3.5rem;
            }

            .presentation-section {
                min-height: auto;
                display: block;
            }

            .hero-visual {
                min-height: 450px;
            }

            .hero-dashboard-shell {
                top: 48%;
                width: min(78vw, 430px);
            }

            .hero-dashboard {
                transform: perspective(900px) rotateX(2deg) rotateY(-3deg) rotateZ(.2deg) scale(.88);
            }
        }

        @media (max-width: 640px) {
            .section-anchor { scroll-margin-top: 88px; }

            .landing-section-frame {
                min-height: auto;
            }

            .landing-section-inner {
                padding-inline: 1rem;
                padding-block: 2.75rem;
            }

            .hero-visual {
                min-height: auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding-block: 1rem 0;
            }

            .hero-dashboard-shell {
                position: relative;
                left: auto;
                top: auto;
                width: min(100%, 360px);
                margin-inline: auto;
                transform: none;
            }

            .hero-dashboard {
                transform: scale(.72) !important;
                transform-origin: center;
            }

            .float-a,
            .float-c {
                display: none !important;
            }

            .float-b {
                right: .25rem;
                top: .25rem;
                transform: scale(.78);
                transform-origin: top right;
            }
        }
    </style>
</head>
<body class="overflow-x-hidden bg-ui-canvas font-sans text-ui-text antialiased">
    @php
        $navItems = [
            ['label' => 'Lifecycle', 'href' => '#lifecycle'],
            ['label' => 'Demo', 'href' => '#demo-portal'],
            ['label' => 'Features', 'href' => '#features'],
            ['label' => 'Roles', 'href' => '#roles'],
            ['label' => 'Verification', 'href' => '#verification'],
        ];

        $features = [
            ['title' => 'Programmable Governance Validation', 'copy' => 'Approval conditions, eligibility checks, and assistance rules stay visible to operators without exposing technical complexity.', 'icon' => 'shield-check'],
            ['title' => 'QR Claim Passes', 'copy' => 'Approved members receive clear claim references that merchants can validate before processing assistance.', 'icon' => 'qr-code'],
            ['title' => 'Merchant Reimbursement Tracking', 'copy' => 'Reimbursements move through pending, processing, and released states with operational context.', 'icon' => 'credit-card'],
            ['title' => 'Morph Proof Verification', 'copy' => 'Claim and settlement proof events can be verified for audit integrity without wallet workflows.', 'icon' => 'link'],
            ['title' => 'Audit Reports and Exports', 'copy' => 'Verification logs, settlement records, and governance activity remain ready for review.', 'icon' => 'file-text'],
            ['title' => 'Role-Based Operations', 'copy' => 'Members, admins, merchants, and auditors each get focused workflows on one shared record.', 'icon' => 'layout-dashboard'],
        ];

        $roles = [
            ['role' => 'Member', 'copy' => 'Submit requests, monitor approvals, and present QR claim passes.', 'items' => ['Assistance request forms', 'Claim pass access', 'Notification timeline'], 'icon' => 'ticket'],
            ['role' => 'Admin', 'copy' => 'Review requests, manage programs, and supervise assistance operations.', 'items' => ['Approval queues', 'Program governance', 'Activity monitoring'], 'icon' => 'landmark'],
            ['role' => 'Merchant', 'copy' => 'Validate QR references, process claims, and track reimbursements.', 'items' => ['QR validation', 'Claim processing', 'Settlement status'], 'icon' => 'store'],
            ['role' => 'Auditor', 'copy' => 'Review proof records, timelines, and exportable governance reports.', 'items' => ['Audit timeline', 'Proof references', 'Report exports'], 'icon' => 'shield-check'],
        ];

        $lifecycle = [
            ['title' => 'Request', 'copy' => 'Member submits assistance details.', 'icon' => 'pen-line'],
            ['title' => 'Review', 'copy' => 'Admin validates eligibility.', 'icon' => 'list-checks'],
            ['title' => 'Approved', 'copy' => 'Governance rules confirm the request.', 'icon' => 'check-circle'],
            ['title' => 'QR Issued', 'copy' => 'A claim pass is generated.', 'icon' => 'qr-code'],
            ['title' => 'Merchant Validation', 'copy' => 'The merchant validates the claim.', 'icon' => 'store'],
            ['title' => 'Settlement Released', 'copy' => 'Reimbursement moves to settlement.', 'icon' => 'credit-card'],
            ['title' => 'Morph Proof Recorded', 'copy' => 'Proof becomes audit-ready.', 'icon' => 'link'],
        ];
    @endphp

    <div id="scroll-bar" class="fixed left-0 top-0 z-[60] h-0.5 w-0 bg-gradient-to-r from-ui-action to-ui-proof"></div>

    <header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-ui-border/80 bg-white/90 px-4 py-3 shadow-lg shadow-ui-anchor/10 backdrop-blur">
                <div class="flex items-center justify-between gap-4">
                    <a href="/" class="flex min-w-0 items-center gap-3" aria-label="EduNexUs home">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-ui-border/80">
                            <x-application-logo class="h-full w-full scale-[1.42]" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-base font-black tracking-tight text-ui-anchor">EduNexUs</span>
                            <span class="block truncate text-xs font-medium text-ui-subtext">Governance-backed assistance operations</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-6 text-sm font-semibold text-ui-subtext lg:flex" aria-label="Primary navigation">
                        @foreach($navItems as $item)
                            <a href="{{ $item['href'] }}" class="transition hover:text-ui-action">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>

                    <div class="hidden items-center gap-2 lg:flex">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-dark">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-bold text-ui-anchor transition hover:bg-ui-canvas">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-dark">Register</a>
                                @endif
                            @endauth
                        @endif
                    </div>

                    <button type="button"
                            class="inline-flex h-10 w-10 flex-col items-center justify-center gap-1 rounded-xl border border-ui-border bg-white text-ui-anchor shadow-sm transition hover:border-ui-action/20 hover:bg-ui-canvas lg:hidden"
                            @click="open = ! open"
                            :aria-expanded="open.toString()"
                            aria-controls="mobile-nav"
                            aria-label="Toggle navigation">
                        <span class="hamburger-line" :class="open ? 'translate-y-[6px] rotate-45' : ''"></span>
                        <span class="hamburger-line" :class="open ? 'opacity-0' : ''"></span>
                        <span class="hamburger-line" :class="open ? '-translate-y-[6px] -rotate-45' : ''"></span>
                    </button>
                </div>

                <div id="mobile-nav"
                     x-cloak
                     x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     @click.outside="open = false"
                     class="mt-4 rounded-2xl border border-ui-border bg-white/95 p-3 shadow-xl shadow-ui-anchor/10 backdrop-blur lg:hidden">
                    <nav class="grid gap-1 text-sm font-semibold text-ui-subtext" aria-label="Mobile navigation">
                        @foreach($navItems as $item)
                            <a href="{{ $item['href'] }}" class="rounded-xl px-3 py-2 hover:bg-ui-canvas hover:text-ui-action" @click="open = false">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                    @if (Route::has('login'))
                        <div class="mt-3 grid gap-2 border-t border-ui-border pt-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-bold text-white">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl border border-ui-border bg-white px-4 py-2.5 text-sm font-bold text-ui-anchor">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-bold text-white">Register</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-stage landing-section relative bg-gradient-to-br from-[#fbfefc] via-[#edf7f2] to-[#d4e8df] pt-24 sm:pt-28 lg:pt-32">
            <div class="landing-grid pointer-events-none absolute inset-0 opacity-50"></div>
            <div class="pointer-events-none absolute left-1/2 top-20 h-80 w-80 -translate-x-1/2 rounded-full bg-emerald-500/[0.08] blur-3xl"></div>
            <div class="landing-section-inner relative grid min-h-[calc(100svh-1rem)] items-center gap-6 sm:gap-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-12">
                <div data-reveal>
                    <span class="inline-flex items-center gap-2 rounded-full border border-ui-action/15 bg-white/75 px-4 py-2 text-xs font-bold uppercase tracking-[.18em] text-ui-action shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-ui-success proof-pulse"></span>
                        Cooperative fintech infrastructure
                    </span>
                    <h1 class="mt-6 max-w-4xl text-4xl font-black leading-tight tracking-tight text-ui-anchor sm:text-6xl lg:text-7xl">
                        Programmable Cooperative Assistance Infrastructure
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-ui-subtext sm:text-lg">
                        EduNexUs helps Philippine cooperatives move from manual assistance records to programmable approvals, QR claim validation, merchant settlement visibility, and Morph-backed proof integrity.
                    </p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex min-h-[3.25rem] items-center justify-center rounded-2xl bg-ui-action px-6 py-3 text-sm font-bold text-white shadow-lg shadow-ui-anchor/15 transition hover:-translate-y-0.5 hover:bg-primary-dark">
                                Start onboarding
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex min-h-[3.25rem] items-center justify-center rounded-2xl border border-ui-border bg-white/80 px-6 py-3 text-sm font-bold text-ui-anchor shadow-sm transition hover:-translate-y-0.5 hover:border-ui-action/25">
                                Access portal
                            </a>
                        @endif
                        <a href="#demo-portal" class="inline-flex min-h-[3.25rem] items-center justify-center rounded-2xl border border-ui-action/15 bg-white/70 px-6 py-3 text-sm font-bold text-ui-action shadow-sm transition hover:-translate-y-0.5 hover:border-ui-action/30">
                            View demo portals
                        </a>
                    </div>

                    <div class="mt-9 grid gap-3 sm:grid-cols-3">
                        @foreach([
                            ['value' => 'Programmable', 'label' => 'governance rules'],
                            ['value' => 'QR-based', 'label' => 'claim validation'],
                            ['value' => 'Audit-ready', 'label' => 'proof records'],
                        ] as $metric)
                            <div class="rounded-2xl border border-ui-border/80 bg-white/70 px-4 py-3 shadow-sm">
                                <p class="text-sm font-black text-ui-anchor">{{ $metric['value'] }}</p>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">{{ $metric['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="hero-safe-frame hero-visual relative" data-reveal>
                    <div class="float-a absolute left-2 top-8 z-20 hidden rounded-2xl border border-ui-border bg-white/90 px-4 py-3 shadow-xl shadow-ui-anchor/10 backdrop-blur sm:flex lg:left-0 lg:top-10">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-ui-success/10 text-ui-success"><x-icon name="check-circle" size="h-5 w-5" /></span>
                            <span>
                                <span class="block text-[11px] font-semibold uppercase tracking-wider text-ui-subtext">Request approved</span>
                                <span class="block text-xs font-black text-ui-anchor">Program rules passed</span>
                            </span>
                        </div>
                    </div>

                    <div class="float-b absolute right-2 top-20 z-20 rounded-2xl border border-ui-border bg-white/90 px-3 py-2.5 shadow-xl shadow-ui-anchor/10 backdrop-blur sm:px-4 sm:py-3 lg:right-0 lg:top-24">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-ui-proof/10 text-ui-proof"><x-icon name="qr-code" size="h-5 w-5" /></span>
                            <span>
                                <span class="block text-[11px] font-semibold uppercase tracking-wider text-ui-subtext">Claim pass</span>
                                <span class="block text-xs font-black text-ui-anchor">Ready for validation</span>
                            </span>
                        </div>
                    </div>

                    <div class="float-c absolute bottom-12 right-8 z-20 rounded-2xl border border-ui-border bg-white/90 px-4 py-3 shadow-xl shadow-ui-anchor/10 backdrop-blur lg:bottom-14 lg:right-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-ui-proof"><x-icon name="link" size="h-5 w-5" /></span>
                            <span>
                                <span class="block text-[11px] font-semibold uppercase tracking-wider text-ui-subtext">Morph proof</span>
                                <span class="block text-xs font-black text-ui-anchor">Audit record stored</span>
                            </span>
                        </div>
                    </div>

                    <div class="hero-dashboard-shell">
                    <div class="hero-dashboard overflow-hidden rounded-[1.75rem] border border-emerald-900/10 bg-[#123b34] text-white">
                        <div class="flex items-center justify-between border-b border-white/10 bg-white/[0.04] px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-xl bg-white/95 p-0 ring-1 ring-white/20"><x-application-logo class="h-full w-full scale-[1.42]" /></span>
                                <div>
                                    <p class="text-sm font-black">EduNexUs Operations</p>
                                    <p class="text-xs text-emerald-50/55">Assistance lifecycle monitor</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-emerald-300/10 px-3 py-1 text-xs font-bold text-emerald-100 ring-1 ring-emerald-200/20">Live</span>
                        </div>
                        <div class="space-y-3 p-6">
                            @foreach([
                                ['icon' => 'file-text', 'title' => 'Assistance request', 'copy' => 'Member details submitted', 'tone' => 'text-emerald-100 bg-white/[0.06] border-white/10'],
                                ['icon' => 'list-checks', 'title' => 'Governance review', 'copy' => 'Eligibility and rules validated', 'tone' => 'text-amber-100 bg-amber-400/[0.08] border-amber-200/20'],
                                ['icon' => 'qr-code', 'title' => 'QR claim pass', 'copy' => 'Merchant validation ready', 'tone' => 'text-teal-100 bg-teal-300/[0.08] border-teal-200/20'],
                                ['icon' => 'link', 'title' => 'Morph proof', 'copy' => 'Settlement proof integrity', 'tone' => 'text-cyan-100 bg-cyan-300/[0.08] border-cyan-200/20'],
                            ] as $row)
                                <div class="flex items-center gap-4 rounded-2xl border px-4 py-3 {{ $row['tone'] }}">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10"><x-icon :name="$row['icon']" size="h-5 w-5" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-black">{{ $row['title'] }}</p>
                                        <p class="truncate text-xs text-white/50">{{ $row['copy'] }}</p>
                                    </div>
                                    <x-icon name="chevron-right" size="h-4 w-4 text-white/35" />
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between border-t border-white/10 px-6 py-4 text-xs">
                            <span class="font-semibold text-white/45">Powered by Morph verification infrastructure</span>
                            <span class="font-black text-emerald-100">Audit-ready</span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="lifecycle" class="section-anchor landing-section bg-gradient-to-br from-[#f6fbf8] via-[#edf6f1] to-[#dfeee8]">
            <div class="landing-grid pointer-events-none absolute inset-0 opacity-35"></div>
            <div class="pointer-events-none absolute right-0 top-16 h-72 w-72 rounded-full bg-cyan-400/[0.10] blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 left-8 h-72 w-72 rounded-full bg-emerald-500/[0.08] blur-3xl"></div>
            <div class="landing-section-frame">
            <div class="landing-section-inner">
                <div class="relative max-w-3xl" data-reveal>
                    <p class="text-sm font-bold uppercase tracking-[.18em] text-ui-action">Assistance lifecycle</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-ui-anchor sm:text-5xl">A visible workflow from request to proof.</h2>
                    <p class="mt-4 text-base leading-7 text-ui-subtext">From member request to merchant validation, payout simulation, and proof recording, each stage stays connected to one operational record that can support audits and settlement review.</p>
                </div>
                <div class="relative mt-8 rounded-[2rem] border border-ui-border/80 bg-white/80 p-3 shadow-[0_24px_60px_rgba(6,78,59,0.10)] ring-1 ring-white/80 backdrop-blur sm:mt-10 sm:p-4" data-reveal>
                    <div class="absolute left-10 right-10 top-[4.05rem] hidden h-1 rounded-full stage-rail lg:block"></div>
                    <div class="grid gap-4 lg:grid-cols-7">
                    @foreach($lifecycle as $index => $step)
                        <div class="group relative rounded-2xl border border-ui-border/80 bg-gradient-to-br from-white to-[#f4faf7] p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-ui-action/20 hover:shadow-md sm:p-4">
                            <div class="absolute bottom-full left-1/2 hidden h-4 w-px -translate-x-1/2 bg-gradient-to-b from-ui-action/20 to-ui-border lg:block"></div>
                            <div class="absolute -left-2 top-[1.85rem] hidden h-3 w-3 rounded-full border border-ui-action/20 bg-white shadow-sm lg:block"></div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="relative z-10 flex h-10 w-10 items-center justify-center rounded-2xl bg-ui-action text-white shadow-lg shadow-ui-anchor/15 ring-4 ring-white transition group-hover:bg-primary-dark sm:h-11 sm:w-11"><x-icon :name="$step['icon']" size="h-5 w-5" /></span>
                                <span class="text-xs font-black text-ui-subtext/50">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3 class="mt-4 text-sm font-black text-ui-anchor">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-xs leading-5 text-ui-subtext">{{ $step['copy'] }}</p>
                            @if($index < count($lifecycle) - 1)
                                <div class="mt-4 flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-ui-subtext/55 lg:hidden">
                                    <span class="h-px flex-1 bg-ui-border"></span>
                                    <span>Next</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                    <div class="mt-4 rounded-2xl border border-ui-border/70 bg-gradient-to-r from-ui-action/10 via-cyan-50 to-ui-action/10 px-4 py-3 text-sm font-semibold text-ui-anchor">
                        Connected workflow: member request &rarr; admin review &rarr; QR/reference &rarr; merchant validation &rarr; programmable rules &rarr; settlement visibility &rarr; Morph proof.
                    </div>
                </div>
            </div>
            </div>
        </section>

        <section class="steps-scroll-section section-anchor relative h-[350vh] bg-[#eaf3ee]">
            <div class="sticky top-0 flex h-screen items-center overflow-hidden border-y border-ui-border bg-gradient-to-br from-[#f5fbf8] via-[#eaf3ee] to-[#d9ebe4]">
                <div class="pointer-events-none absolute inset-0 landing-grid opacity-35"></div>
                <div id="step-glow" class="pointer-events-none absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-500/[0.08] blur-3xl transition-colors duration-500"></div>

                <div class="relative mx-auto grid w-full max-w-7xl gap-4 px-4 sm:gap-8 sm:px-6 lg:grid-cols-[140px_1fr_340px] lg:px-8">
                    <div class="hidden flex-col justify-center gap-5 lg:flex">
                        <p id="step-fraction" class="text-xs font-black uppercase tracking-[.18em] text-ui-subtext">01 / 04</p>
                        <div class="flex flex-col gap-3">
                            @foreach(range(0, 3) as $dot)
                                <span class="step-dot h-2 w-2 rounded-full bg-ui-border transition-all duration-300" data-dot="{{ $dot }}"></span>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative flex min-h-[500px] items-center justify-center [perspective:1200px] sm:min-h-[560px]">
                        @foreach([
                            ['title' => 'Submit Request', 'icon' => 'pen-line', 'accent' => '#047857'],
                            ['title' => 'Admin Review', 'icon' => 'list-checks', 'accent' => '#b45309'],
                            ['title' => 'QR Claim Pass', 'icon' => 'qr-code', 'accent' => '#0f766e'],
                            ['title' => 'Morph Proof', 'icon' => 'link', 'accent' => '#0891b2'],
                        ] as $index => $card)
                            <div class="step-card absolute w-[min(92vw,430px)] transition-[opacity,transform] duration-500 ease-out"
                                 data-step-card="{{ $index }}"
                                 style="opacity: {{ $index === 0 ? '1' : '0' }}; transform: translateX({{ $index === 0 ? '0' : '110' }}%) rotateY({{ $index === 0 ? '0' : '18' }}deg) scale({{ $index === 0 ? '1' : '.92' }});">
                                <div class="overflow-hidden rounded-[1.75rem] border border-white/10 bg-[#123b34] text-white shadow-[0_28px_70px_rgba(6,78,59,0.24)]">
                                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3 sm:px-6 sm:py-4">
                                        <span class="text-[11px] font-black uppercase tracking-[.16em] text-emerald-100/70 sm:text-xs sm:tracking-[.18em]">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} of 04</span>
                                        <span class="rounded-full bg-white/[0.07] px-3 py-1 text-xs font-bold text-white/80 ring-1 ring-white/10">{{ $card['title'] }}</span>
                                    </div>

                                    <div class="p-4 sm:p-6">
                                        @if($index === 0)
                                            <div class="relative rounded-2xl border border-white/10 bg-white/[0.04] p-4 sm:p-5">
                                                <div class="space-y-3">
                                                    <div class="h-9 rounded-lg border border-white/10 bg-white/[0.06]"></div>
                                                    <div class="h-9 rounded-lg border border-white/10 bg-white/[0.06]"></div>
                                                    <div class="h-16 rounded-lg border border-white/10 bg-white/[0.06]"></div>
                                                    <div id="s1btn" class="button-confirm flex h-11 items-center justify-center rounded-xl bg-emerald-600 text-sm font-black text-white transition">Submit request</div>
                                                </div>
                                                <svg id="s1cursor" class="cursor-submit pointer-events-none absolute right-7 top-10 h-8 w-8 text-white drop-shadow-lg" viewBox="0 0 24 32" fill="none" aria-hidden="true">
                                                    <path d="M3 3v23l5.6-5.4 3.4 8.4 4.1-1.7-3.3-8.1H21L3 3Z" fill="currentColor" stroke="rgba(15,47,44,.35)" stroke-width="1.2" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="relative rounded-2xl border border-amber-200/20 bg-amber-300/[0.08] p-4 sm:p-5">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-bold uppercase tracking-[.16em] text-amber-100/70">Assistance request</span>
                                                    <span id="s2status" class="rounded-full bg-amber-300/10 px-3 py-1 text-xs font-bold text-amber-100 ring-1 ring-amber-200/20">Pending</span>
                                                </div>
                                                <div class="mt-5 space-y-3">
                                                    <div class="h-12 rounded-xl bg-white/[0.06]"></div>
                                                    <div class="h-12 rounded-xl bg-white/[0.06]"></div>
                                                </div>
                                                <div class="mt-5 grid grid-cols-2 gap-3">
                                                    <div class="flex h-11 items-center justify-center rounded-xl border border-white/10 text-sm font-bold text-white/45">Decline</div>
                                                    <div id="s2approve" class="button-confirm flex h-11 items-center justify-center rounded-xl bg-amber-500/15 text-sm font-black text-amber-100 ring-1 ring-amber-200/20">Approve</div>
                                                </div>
                                                <svg id="s2cursor" class="cursor-approve pointer-events-none absolute right-10 top-12 h-8 w-8 text-white drop-shadow-lg" viewBox="0 0 24 32" fill="none" aria-hidden="true">
                                                    <path d="M3 3v23l5.6-5.4 3.4 8.4 4.1-1.7-3.3-8.1H21L3 3Z" fill="currentColor" stroke="rgba(15,47,44,.35)" stroke-width="1.2" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        @elseif($index === 2)
                                            <div class="rounded-2xl border border-teal-200/20 bg-teal-300/[0.08] p-4 text-center sm:p-5">
                                                <p class="text-xs font-black uppercase tracking-[.18em] text-teal-100/70">Claim pass</p>
                                                <div class="relative mx-auto mt-5 inline-block rounded-xl bg-white p-3 shadow-lg">
                                                    <svg width="116" height="116" viewBox="0 0 116 116" aria-hidden="true">
                                                        <rect width="116" height="116" rx="10" fill="#ffffff"/>
                                                        <g fill="#064E3B">
                                                            <rect x="10" y="10" width="28" height="28" rx="3"/><rect x="17" y="17" width="14" height="14" rx="2" fill="#ffffff"/>
                                                            <rect x="78" y="10" width="28" height="28" rx="3"/><rect x="85" y="17" width="14" height="14" rx="2" fill="#ffffff"/>
                                                            <rect x="10" y="78" width="28" height="28" rx="3"/><rect x="17" y="85" width="14" height="14" rx="2" fill="#ffffff"/>
                                                            @foreach([[48,10],[58,10],[68,10],[48,20],[68,20],[48,30],[58,30],[10,48],[20,48],[48,48],[68,48],[78,48],[98,48],[48,58],[58,58],[78,58],[88,58],[48,68],[68,68],[88,68],[98,68],[48,78],[58,78],[78,78],[48,88],[68,88],[88,88],[48,98],[58,98],[78,98],[98,98]] as $sq)
                                                                <rect x="{{ $sq[0] }}" y="{{ $sq[1] }}" width="7" height="7" rx="1"/>
                                                            @endforeach
                                                        </g>
                                                    </svg>
                                                    <span class="qr-scan-line absolute left-3 right-3 h-0.5 rounded-full bg-gradient-to-r from-transparent via-teal-400 to-transparent shadow-[0_0_12px_rgba(20,184,166,0.65)]"></span>
                                                </div>
                                                <p id="s3badge" class="mt-5 inline-flex rounded-full bg-teal-300/10 px-4 py-1.5 text-xs font-black text-teal-100 ring-1 ring-teal-200/20">Scanning</p>
                                            </div>
                                        @else
                                            <div class="rounded-2xl border border-cyan-200/20 bg-cyan-300/[0.08] p-4 sm:p-5">
                                                <p class="text-xs font-black uppercase tracking-[.18em] text-cyan-100/70">Verification record</p>
                                                <div class="mt-6 flex items-center justify-center gap-0">
                                                    @foreach(range(1, 3) as $block)
                                                        <div class="proof-block flex-1 rounded-xl border border-cyan-200/20 bg-cyan-300/[0.08] p-3 text-center opacity-40 transition duration-500" data-proof-block="{{ $block }}">
                                                            <p class="text-[10px] font-black text-cyan-100">BLOCK {{ $block }}</p>
                                                            <div class="mt-2 h-1.5 rounded-full bg-cyan-100/20"></div>
                                                            <div class="mx-auto mt-2 h-1.5 w-1/2 rounded-full bg-cyan-100/15"></div>
                                                        </div>
                                                        @if($block < 3)
                                                            <div class="h-px w-5 bg-cyan-100/25"></div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="mt-6 rounded-xl border border-cyan-200/20 bg-cyan-300/[0.08] px-4 py-3 font-mono text-xs text-cyan-100/65">0x4e3b...f8cd</div>
                                                <p id="s4badge" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-cyan-300/[0.08] py-3 text-sm font-black text-cyan-100 ring-1 ring-cyan-200/20"><span class="h-2 w-2 rounded-full bg-cyan-300 proof-pulse"></span>Proof confirmed</p>
                                            </div>
                                        @endif

                                        <div class="mt-6 h-1.5 overflow-hidden rounded-full bg-white/10">
                                            <div class="step-progress h-full w-0 rounded-full" data-step-bar="{{ $index }}" style="background: {{ $card['accent'] }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden flex-col justify-center lg:flex">
                        @foreach([
                            ['title' => 'Submit with structure', 'copy' => 'Members submit assistance details into an operational workflow instead of an informal message chain.'],
                            ['title' => 'Review with governance', 'copy' => 'Cooperative officers validate eligibility and program rules with a clearer audit trail.'],
                            ['title' => 'Validate at merchant', 'copy' => 'The QR pass makes claim presentation simple while keeping validation controlled.'],
                            ['title' => 'Record proof integrity', 'copy' => 'Morph-backed proof records support settlement transparency and audit confidence.'],
                        ] as $index => $panel)
                            <div class="step-text-panel absolute max-w-sm transition duration-500" data-step-text="{{ $index }}" style="opacity: {{ $index === 0 ? '1' : '0' }}; transform: translateY({{ $index === 0 ? '0' : '18px' }});">
                                <h3 class="text-2xl font-black text-ui-anchor">{{ $panel['title'] }}</h3>
                                <p class="mt-4 leading-7 text-ui-subtext">{{ $panel['copy'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <x-demo-portal />

        <section id="features" class="section-anchor landing-section bg-gradient-to-br from-white via-[#f7fbf9] to-[#eef7f3]">
            <div class="pointer-events-none absolute left-0 top-0 h-72 w-72 rounded-full bg-emerald-400/[0.07] blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-72 w-72 rounded-full bg-cyan-400/[0.08] blur-3xl"></div>
            <div class="landing-section-frame">
            <div class="landing-section-inner">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between" data-reveal>
                    <div class="max-w-3xl">
                        <p class="text-sm font-bold uppercase tracking-[.18em] text-ui-action">Platform capabilities</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-ui-anchor sm:text-5xl">Premium motion, operational purpose.</h2>
                    </div>
                    <p class="max-w-md leading-7 text-ui-subtext">Built for cooperative assistance today, with reusable workflow patterns for broader governance and reimbursement ecosystems tomorrow.</p>
                </div>

                <div class="mt-8 grid gap-3 sm:gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($features as $feature)
                        <article class="feature-card rounded-2xl border border-ui-border bg-white/85 p-4 shadow-[0_12px_34px_rgba(6,78,59,0.07)] ring-1 ring-white/70 backdrop-blur transition hover:-translate-y-1 hover:border-ui-action/20 hover:shadow-[0_18px_42px_rgba(6,78,59,0.10)] sm:p-6" data-reveal>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                                <x-icon :name="$feature['icon']" size="h-5 w-5" />
                            </div>
                            <h3 class="mt-5 text-base font-black text-ui-anchor">{{ $feature['title'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-ui-subtext">{{ $feature['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
            </div>
        </section>

        <section id="roles" class="section-anchor landing-section bg-gradient-to-br from-[#edf6f1] via-ui-canvas to-[#dcece6]">
            <div class="landing-grid pointer-events-none absolute inset-0 opacity-25"></div>
            <div class="landing-section-frame">
            <div class="landing-section-inner">
                <div class="max-w-3xl" data-reveal>
                    <p class="text-sm font-bold uppercase tracking-[.18em] text-ui-action">Role-based operations</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-ui-anchor sm:text-5xl">Every role is readable without hover tricks.</h2>
                    <p class="mt-4 leading-7 text-ui-subtext">Each role sees the part of the workflow they need, while the system keeps one shared assistance and audit record.</p>
                </div>
                <div class="mt-8 grid gap-3 sm:gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($roles as $role)
                        <article class="rounded-2xl border border-ui-border bg-white/90 p-4 shadow-[0_12px_34px_rgba(6,78,59,0.07)] ring-1 ring-white/70 backdrop-blur transition hover:-translate-y-0.5 hover:border-ui-action/20 hover:shadow-[0_18px_42px_rgba(6,78,59,0.10)] sm:p-6" data-reveal>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                                <x-icon :name="$role['icon']" size="h-5 w-5" />
                            </div>
                            <h3 class="mt-5 text-lg font-black text-ui-anchor">{{ $role['role'] }}</h3>
                            <p class="mt-3 text-sm leading-6 text-ui-subtext">{{ $role['copy'] }}</p>
                            <ul class="mt-5 space-y-2">
                                @foreach($role['items'] as $item)
                                    <li class="flex items-center gap-2 text-sm text-ui-subtext">
                                        <span class="h-1.5 w-1.5 rounded-full bg-ui-action"></span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </div>
            </div>
        </section>

        <section id="verification" class="section-anchor landing-section bg-gradient-to-br from-[#123b34] via-[#0e332d] to-[#0b2f25] text-white">
            <div class="landing-grid pointer-events-none absolute inset-0 opacity-10"></div>
            <div class="pointer-events-none absolute right-0 top-0 h-96 w-96 rounded-full bg-cyan-300/[0.08] blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 left-0 h-96 w-96 rounded-full bg-emerald-300/[0.06] blur-3xl"></div>
            <div class="landing-section-frame">
            <div class="landing-section-inner relative grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
                <div data-reveal>
                    <p class="text-sm font-bold uppercase tracking-[.18em] text-emerald-100/70">Morph verification</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">Blockchain-backed verification infrastructure for settlement transparency.</h2>
                    <p class="mt-5 leading-8 text-emerald-50/70">Proof recording is audit-focused and operational. EduNexUs uses Morph-backed records and demo-safe testnet settlement proof to support claim integrity without exposing wallet-first complexity to normal users.</p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        @foreach(['Tamper-resistant records', 'Public verification', 'Settlement proof integrity', 'Audit-ready visibility'] as $tag)
                            <span class="rounded-full bg-white/[0.07] px-4 py-2 text-sm font-bold text-emerald-50 ring-1 ring-white/10">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="audit-console rounded-[1.75rem] border border-white/10 bg-white/[0.06] p-4 shadow-[0_28px_70px_rgba(0,0,0,0.18)] sm:p-5" data-reveal>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach([
                            ['label' => 'Proof type', 'value' => 'Claim validation'],
                            ['label' => 'Reference', 'value' => 'QR-2026-0008'],
                            ['label' => 'Status', 'value' => 'Confirmed'],
                        ] as $proof)
                            <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-3 sm:p-4">
                                <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-50/45">{{ $proof['label'] }}</p>
                                <p class="mt-3 text-sm font-black text-white">{{ $proof['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.06] p-4 sm:p-5">
                        <div class="flex items-center gap-4">
                            <span class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-300/10 text-cyan-100 ring-1 ring-cyan-200/20">
                                <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-emerald-300 proof-pulse"></span>
                                <x-icon name="link" size="h-6 w-6" />
                            </span>
                            <div>
                                <p class="font-black">Proof record stored</p>
                                <p class="mt-1 font-mono text-xs text-emerald-50/55"><span class="audit-hash">0x4e3b...f8cd</span></p>
                            </div>
                        </div>
                        <div class="audit-status mt-4 rounded-xl border border-emerald-200/15 bg-emerald-300/[0.08] px-4 py-3 text-sm font-bold text-emerald-50">
                            <span id="audit-live-message" class="audit-live-message">Recording proof bundle...</span>
                        </div>
                        <div class="mt-4 grid gap-3">
                            @foreach(['Verification log linked', 'Settlement lifecycle visible', 'Export-ready audit context'] as $row)
                                <div class="audit-row flex items-center gap-3 rounded-xl bg-white/[0.05] px-4 py-3 text-sm font-semibold text-emerald-50/75">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                    {{ $row }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>

        <section id="governance" class="section-anchor landing-section bg-gradient-to-br from-ui-canvas via-[#eef7f3] to-white">
            <div class="pointer-events-none absolute left-1/3 top-0 h-80 w-80 rounded-full bg-emerald-400/[0.06] blur-3xl"></div>
            <div class="landing-section-frame">
            <div class="landing-section-inner">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                    <div data-reveal>
                        <p class="text-sm font-bold uppercase tracking-[.18em] text-ui-action">Reporting and governance</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-ui-anchor sm:text-5xl">Operational visibility without visual noise.</h2>
                    <p class="mt-4 leading-7 text-ui-subtext">Compact records show approvals, QR validation, simulated PHP/GCash payout movement, proof references, and export-ready context without overstating financial automation.</p>
                    </div>
                    <div class="overflow-hidden rounded-2xl border border-ui-border bg-white shadow-[0_18px_44px_rgba(15,47,44,0.08)]" data-reveal>
                        <div class="border-b border-ui-border bg-gradient-to-r from-[#f8fdfb] to-ui-muted/40 px-5 py-4">
                            <p class="text-sm font-black text-ui-anchor">Governance console preview</p>
                            <p class="mt-1 text-xs text-ui-subtext">Compact records for review, export, and audit follow-up.</p>
                        </div>
                        <div class="divide-y divide-ui-border">
                            @foreach([
                                ['event' => 'Admin approved assistance request', 'state' => 'Approved', 'time' => '08:45 AM'],
                                ['event' => 'Merchant validated QR claim pass', 'state' => 'Claimed', 'time' => '09:12 AM'],
                                ['event' => 'Settlement proof recorded on Morph', 'state' => 'Confirmed', 'time' => '09:18 AM'],
                            ] as $row)
                                <div class="grid gap-3 px-5 py-4 sm:grid-cols-[1fr_auto_auto] sm:items-center">
                                    <p class="text-sm font-bold text-ui-anchor">{{ $row['event'] }}</p>
                                    <span class="w-fit rounded-full bg-ui-action/10 px-3 py-1 text-xs font-bold text-ui-action">{{ $row['state'] }}</span>
                                    <p class="text-sm text-ui-subtext sm:text-right">{{ $row['time'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>

        <section class="closing-section landing-section bg-gradient-to-br from-ui-action via-primary-dark to-ui-anchor text-white">
            <div class="landing-section-inner max-w-5xl text-center" data-reveal>
                <p class="text-sm font-bold uppercase tracking-[.18em] text-emerald-100/75">Institutional assistance operations</p>
                <h2 class="mt-4 text-3xl font-black tracking-tight sm:text-5xl">Modernize cooperative assistance operations with programmable governance and blockchain-backed verification.</h2>
                <p class="mx-auto mt-5 max-w-3xl leading-8 text-emerald-50/80">Bring approvals, QR validation, merchant claim processing, settlement visibility, and blockchain-backed audit proof into one operational system designed for cooperative assistance workflows.</p>
                <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex min-h-[3.25rem] items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-black text-ui-action shadow-lg transition hover:bg-emerald-50">Create account</a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex min-h-[3.25rem] items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-6 py-3 text-sm font-black text-white transition hover:bg-white/15">Sign in</a>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-ui-border bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_auto] lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-ui-border"><x-application-logo class="h-full w-full scale-[1.42]" /></span>
                    <div>
                        <p class="font-black text-ui-anchor">EduNexUs</p>
                        <p class="text-sm text-ui-subtext">Programmable assistance and settlement infrastructure</p>
                    </div>
                </div>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-ui-subtext">Built for approval governance, QR claim validation, merchant reimbursement tracking, and Morph-backed settlement proof.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm font-bold text-ui-subtext lg:justify-end">
                @foreach($navItems as $item)
                    <a href="{{ $item['href'] }}" class="transition hover:text-ui-action">{{ $item['label'] }}</a>
                @endforeach
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="transition hover:text-ui-action">Login</a>
                @endif
            </div>
        </div>
        <div class="border-t border-ui-border px-4 py-4 text-center text-xs text-ui-subtext sm:px-6">
            &copy; {{ date('Y') }} EduNexUs. Operational proof support powered by Morph verification infrastructure.
        </div>
    </footer>

    <script>
        (function () {
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const bar = document.getElementById('scroll-bar');
            const dashboard = document.querySelector('.hero-dashboard');
            const revealItems = document.querySelectorAll('[data-reveal]');
            const stepsSection = document.querySelector('.steps-scroll-section');
            const cards = document.querySelectorAll('[data-step-card]');
            const texts = document.querySelectorAll('[data-step-text]');
            const dots = document.querySelectorAll('[data-dot]');
            const bars = document.querySelectorAll('[data-step-bar]');
            const stepFraction = document.getElementById('step-fraction');
            const stepGlow = document.getElementById('step-glow');
            const submitButton = document.getElementById('s1btn');
            const approveButton = document.getElementById('s2approve');
            const approveStatus = document.getElementById('s2status');
            const qrBadge = document.getElementById('s3badge');
            const proofBlocks = document.querySelectorAll('[data-proof-block]');
            const proofBadge = document.getElementById('s4badge');
            const auditLiveMessage = document.getElementById('audit-live-message');
            const auditMessages = [
                'Recording proof...',
                'Log linked...',
                'Lifecycle visible...',
                'Export ready...',
                'Proof reference set...'
            ];
            const stepColors = ['rgba(4,120,87,.09)', 'rgba(180,83,9,.09)', 'rgba(15,118,110,.09)', 'rgba(8,145,178,.09)'];
            let ticking = false;
            let auditIndex = 0;

            function updateBar() {
                if (!bar) return;
                const max = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
            }

            function updateHero() {
                if (!dashboard || reducedMotion) return;
                const y = Math.min(window.scrollY, 600);
                if (window.innerWidth <= 640) {
                    dashboard.style.transform = 'none';
                    return;
                }

                if (window.innerWidth <= 1023) {
                    dashboard.style.transform = `perspective(900px) rotateX(${2 + y * 0.002}deg) rotateY(${-3 + y * 0.001}deg) rotateZ(.2deg) scale(.88) translateY(${y * 0.018}px)`;
                    return;
                }

                dashboard.style.transform = `perspective(1200px) rotateX(${5 + y * 0.006}deg) rotateY(${-8 + y * 0.003}deg) rotateZ(.5deg) translateY(${y * 0.03}px)`;
            }

            function updateSteps() {
                if (!stepsSection) return;
                const rect = stepsSection.getBoundingClientRect();
                const maxScroll = stepsSection.offsetHeight - window.innerHeight;
                const progress = Math.max(0, Math.min(1, -rect.top / maxScroll));
                const raw = progress * 4;
                const active = Math.min(Math.floor(raw), 3);
                const local = raw - active;

                if (stepFraction) stepFraction.textContent = String(active + 1).padStart(2, '0') + ' / 04';
                if (stepGlow) stepGlow.style.background = stepColors[active];

                dots.forEach((dot, index) => {
                    dot.style.background = index === active ? '#064E3B' : index < active ? '#047857' : '#D5E2DC';
                    dot.style.transform = index === active ? 'scale(1.55)' : 'scale(1)';
                });

                cards.forEach((card, index) => {
                    let tx = 110;
                    let ry = 18;
                    let scale = .92;
                    let opacity = 0;

                    if (index < active) {
                        tx = -105;
                        ry = -16;
                        opacity = 0;
                    } else if (index === active) {
                        tx = 0;
                        ry = 0;
                        scale = 1;
                        opacity = 1;
                    } else if (index === active + 1) {
                        opacity = .18;
                    }

                    card.style.transform = `translateX(${tx}%) rotateY(${ry}deg) scale(${scale})`;
                    card.style.opacity = opacity;
                });

                texts.forEach((panel, index) => {
                    panel.style.opacity = index === active ? '1' : '0';
                    panel.style.transform = index === active ? 'translateY(0)' : 'translateY(18px)';
                });

                bars.forEach((progressBar, index) => {
                    progressBar.style.width = index < active ? '100%' : index === active ? Math.max(8, Math.round(local * 100)) + '%' : '0%';
                });

                if (submitButton) {
                    submitButton.textContent = active === 0 && local > .58 ? 'Request submitted' : 'Submit request';
                    submitButton.classList.toggle('bg-emerald-700', active === 0 && local > .58);
                }

                if (approveButton && approveStatus) {
                    const approved = active === 1 && local > .58;
                    approveButton.textContent = approved ? 'Approved' : 'Approve';
                    approveStatus.textContent = approved ? 'Approved' : 'Pending';
                    approveStatus.classList.toggle('bg-emerald-300/10', approved);
                    approveStatus.classList.toggle('text-emerald-100', approved);
                }

                if (qrBadge) {
                    qrBadge.textContent = active === 2 && local > .55 ? 'Validated' : 'Scanning';
                }

                proofBlocks.forEach((block, index) => {
                    const visible = active === 3 && local > (.2 + index * .16);
                    block.style.opacity = visible ? '1' : '.4';
                    block.style.transform = visible ? 'translateY(0)' : 'translateY(8px)';
                });

                if (proofBadge) {
                    proofBadge.style.opacity = active === 3 && local > .7 ? '1' : '.72';
                }
            }

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });

                revealItems.forEach((item) => observer.observe(item));
            } else {
                revealItems.forEach((item) => item.classList.add('is-visible'));
            }

            function onScroll() {
                if (ticking) return;
                window.requestAnimationFrame(() => {
                    updateBar();
                    updateHero();
                    updateSteps();
                    ticking = false;
                });
                ticking = true;
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll);
            updateBar();
            updateHero();
            updateSteps();

            if (auditLiveMessage && !reducedMotion) {
                window.setInterval(() => {
                    auditIndex = (auditIndex + 1) % auditMessages.length;
                    auditLiveMessage.textContent = auditMessages[auditIndex];
                }, 3600);
            }

            if (reducedMotion) {
                revealItems.forEach((item) => item.classList.add('is-visible'));
                if (auditLiveMessage) auditLiveMessage.textContent = auditMessages[auditMessages.length - 1];
            }
        })();
    </script>
</body>
</html>
