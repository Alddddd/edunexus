@props([
    'compact' => false,
])

@php
    $demoMode = (bool) config('app.demo_mode');

    $demoRoles = [
        [
            'role' => 'admin',
            'title' => 'Admin Portal',
            'description' => 'Approve assistance, monitor settlements, review reports, and inspect proof logs.',
            'icon' => 'landmark',
        ],
        [
            'role' => 'member',
            'title' => 'Member Portal',
            'description' => 'Submit assistance requests, track approval status, and access QR claim passes.',
            'icon' => 'ticket',
        ],
        [
            'role' => 'merchant',
            'title' => 'Merchant Portal',
            'description' => 'Validate QR claims, manage payout references, and view settlement history.',
            'icon' => 'store',
        ],
        [
            'role' => 'auditor',
            'title' => 'Auditor Portal',
            'description' => 'Review Morph verification logs, audit reports, and settlement proof records.',
            'icon' => 'shield-check',
        ],
    ];
@endphp

<section id="demo-portal" {{ $attributes->merge(['class' => $compact ? 'rounded-2xl border border-ui-border bg-ui-canvas/70 p-4 sm:p-5' : 'section-anchor landing-section bg-gradient-to-br from-white via-[#f8fbf9] to-[#e4eee9]']) }}>
    <div @class([
        'mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20' => ! $compact,
    ])>
        <div @class([
            'rounded-[1.75rem] border border-ui-border/80 bg-white/[0.82] p-4 shadow-[0_24px_60px_rgba(15,47,44,0.08)] ring-1 ring-white/80 backdrop-blur sm:p-6 lg:p-7' => ! $compact,
            'space-y-5' => $compact,
        ])>
            <div @class([
                'grid gap-6 xl:grid-cols-[minmax(18rem,0.62fr)_minmax(0,1.38fr)] xl:items-start' => ! $compact,
                'space-y-4' => $compact,
            ])>
                <div @class([
                    'flex h-full flex-col justify-between gap-5 rounded-2xl border border-ui-border/70 bg-gradient-to-br from-white to-ui-canvas/70 p-5 shadow-sm sm:p-6' => ! $compact,
                ]) data-reveal>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.18em] text-ui-action">
                            Demo Portal
                        </p>

                        <h2 @class([
                            'font-black tracking-tight text-ui-anchor',
                            'mt-3 text-3xl leading-tight sm:text-4xl xl:text-5xl' => ! $compact,
                            'mt-2 text-xl leading-snug' => $compact,
                        ])>
                            Role-based demo access.
                        </h2>

                        <p @class([
                            'text-ui-subtext',
                            'mt-4 text-base leading-7' => ! $compact,
                            'mt-2 text-sm leading-6' => $compact,
                        ])>
                            Explore EduNexUs through seeded role accounts. Demo payouts are simulated for safety, while settlement proof uses demo-safe testnet infrastructure.
                        </p>
                    </div>

                    @unless($demoMode)
                        <div class="rounded-2xl border border-ui-proof/15 bg-cyan-50/75 px-4 py-3.5 text-sm leading-6 text-ui-subtext shadow-sm ring-1 ring-white/70">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-ui-proof/10 text-ui-proof">
                                    <x-icon name="info" size="h-4 w-4" />
                                </span>

                                <p>
                                    One-click demo access is available for hackathon judging and evaluation.
                                </p>
                            </div>
                        </div>
                    @endunless
                </div>

                <div @class([
                    'grid items-stretch gap-4',
                    'sm:grid-cols-2 2xl:grid-cols-4' => ! $compact,
                    'sm:grid-cols-2' => $compact,
                ])>
                    @foreach($demoRoles as $demoRole)
                        <article class="flex h-full flex-col rounded-2xl border border-ui-border bg-white/[0.94] p-5 shadow-[0_14px_34px_rgba(15,47,44,0.065)] ring-1 ring-white/80 backdrop-blur transition hover:-translate-y-0.5 hover:border-ui-action/20 hover:shadow-[0_18px_42px_rgba(15,47,44,0.09)] sm:p-6" data-reveal>
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                                    <x-icon :name="$demoRole['icon']" size="h-5 w-5" />
                                </span>

                                <div class="min-w-0">
                                    <h3 class="text-base font-black leading-6 text-ui-anchor">
                                        {{ $demoRole['title'] }}
                                    </h3>

                                    <p class="mt-2.5 text-sm leading-6 text-ui-subtext">
                                        {{ $demoRole['description'] }}
                                    </p>
                                </div>
                            </div>

                            @if($demoMode)
                                <form method="POST" action="{{ route('demo-login', $demoRole['role']) }}" class="mt-auto pt-5">
                                    @csrf

                                    <button type="submit" class="inline-flex min-h-[2.875rem] w-full items-center justify-center gap-2 rounded-xl bg-ui-action px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-ui-anchor/10 transition hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-ui-action/20">
                                        Enter {{ $demoRole['title'] }}
                                        <x-icon name="chevron-right" size="h-4 w-4" />
                                    </button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
