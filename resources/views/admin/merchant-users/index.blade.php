@extends('layouts.dashboard')

@section('title', 'Merchant Access Accounts')

@section('content')
    <x-page-header
        title="Merchant Access Accounts"
        eyebrow="Partner Onboarding"
        description="Review lightweight merchant login accounts and whether they are linked to accredited merchant profiles.">
        <x-slot:actions>
            <a href="{{ route('admin.merchant-users.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                Create Access Account
            </a>

            <a href="{{ route('admin.merchants.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                Link Merchant Profile
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Merchant Login Access"
        description="Accounts here are role-locked to merchant access. Accreditation remains controlled by the linked merchant profile.">
        <div class="hidden lg:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Name</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Email</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Linked Merchant</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($merchantUsers as $merchantUser)
                        <tr class="transition hover:bg-ui-canvas/70">
                            <td class="px-5 py-4 font-semibold text-ui-text">{{ $merchantUser->name }}</td>
                            <td class="px-5 py-4 text-ui-subtext">{{ $merchantUser->email }}</td>
                            <td class="px-5 py-4">
                                <x-status-badge
                                    :status="ucfirst($merchantUser->status)"
                                    :tone="strtolower($merchantUser->status) === 'active' ? 'active' : 'neutral'" />
                            </td>
                            <td class="px-5 py-4">
                                @if($merchantUser->merchantProfile)
                                    <p class="font-semibold text-ui-text">{{ $merchantUser->merchantProfile->business_name }}</p>
                                    <p class="mt-1 text-xs text-ui-subtext">{{ $merchantUser->merchantProfile->merchant_category }}</p>
                                @else
                                    <a href="{{ route('admin.merchants.create', ['merchant_user' => $merchantUser->id]) }}"
                                       class="text-sm font-semibold text-ui-action hover:text-ui-anchor">
                                        Link merchant profile
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <p class="font-semibold text-ui-text">No merchant access accounts yet</p>
                                <p class="mt-2 text-sm text-ui-subtext">Create an access account before linking an accredited merchant profile.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-4 lg:hidden">
            @forelse($merchantUsers as $merchantUser)
                <div class="rounded-2xl border border-ui-border bg-ui-surface p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-ui-text">{{ $merchantUser->name }}</p>
                            <p class="mt-1 text-sm text-ui-subtext">{{ $merchantUser->email }}</p>
                        </div>

                        <x-status-badge
                            :status="ucfirst($merchantUser->status)"
                            :tone="strtolower($merchantUser->status) === 'active' ? 'active' : 'neutral'"
                            size="xs" />
                    </div>

                    <div class="mt-4 rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Linked Merchant</p>

                        @if($merchantUser->merchantProfile)
                            <p class="mt-2 font-semibold text-ui-text">{{ $merchantUser->merchantProfile->business_name }}</p>
                            <p class="mt-1 text-sm text-ui-subtext">{{ $merchantUser->merchantProfile->merchant_category }}</p>
                        @else
                            <a href="{{ route('admin.merchants.create', ['merchant_user' => $merchantUser->id]) }}"
                               class="mt-2 inline-flex min-h-10 items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white transition hover:bg-ui-anchor">
                                Link merchant profile
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-ui-border bg-ui-surface p-8 text-center">
                    <p class="font-semibold text-ui-text">No merchant access accounts yet</p>
                    <p class="mt-2 text-sm text-ui-subtext">Create an access account before linking an accredited merchant profile.</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-ui-border bg-ui-canvas/70 px-6 py-4">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $merchantUsers->firstItem() ?? 0 }} to {{ $merchantUsers->lastItem() ?? 0 }} of {{ $merchantUsers->total() }} accounts
                </p>

                <div class="flex justify-center">
                    {{ $merchantUsers->links() }}
                </div>
            </div>
        </div>
    </x-table-card>
@endsection
