@extends('layouts.dashboard')

@section('title', 'Merchants')

@section('content')
<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Merchant Accreditation"
        eyebrow="Partner Network"
        description="Manage accredited merchants and categories used by programmable claim validation.">
        <x-slot:actions>
            <a href="{{ route('admin.merchant-users.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                Create Access Account
            </a>

            <a href="{{ route('admin.merchant-users.index') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                Access Accounts
            </a>

            <a href="{{ route('admin.merchants.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                Add Merchant
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Accredited Merchant Profiles"
        description="Merchant categories must match assistance program categories before claims can be processed.">
        <div class="hidden lg:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Business</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">User Account</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Category</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Contact</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($merchants as $merchant)
                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">
                                    {{ $merchant->business_name }}
                                </p>

                                <p class="mt-1 max-w-xs text-xs leading-5 text-ui-subtext">
                                    {{ $merchant->address ?? 'No address provided' }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-700">
                                    {{ $merchant->user->name }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    {{ $merchant->user->email }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <x-status-badge :status="$merchant->merchant_category" tone="proof" />
                            </td>

                            <td class="px-5 py-4">
                                <x-status-badge :status="$merchant->status" :tone="$merchant->status === 'Active' ? 'active' : 'neutral'" />
                            </td>

                            <td class="px-5 py-4 text-ui-subtext">
                                {{ $merchant->contact_number ?? 'N/A' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.merchants.edit', $merchant) }}"
                                   class="inline-flex min-h-10 items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-ui-action transition hover:bg-teal-50 hover:text-ui-anchor">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-ui-text">
                                    No merchant profiles yet.
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    Add accredited merchants before claim validation begins.
                                </p>

                                <a href="{{ route('admin.merchants.create') }}"
                                   class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                                    Add First Merchant
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80 lg:hidden">
            @forelse($merchants as $merchant)
                <article class="p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-semibold text-ui-text">
                                {{ $merchant->business_name }}
                            </p>

                            <p class="mt-1 text-sm leading-5 text-ui-subtext">
                                {{ $merchant->address ?? 'No address provided' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <x-status-badge :status="$merchant->merchant_category" tone="proof" />
                            <x-status-badge :status="$merchant->status" :tone="$merchant->status === 'Active' ? 'active' : 'neutral'" />
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                User Account
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $merchant->user->name }}
                            </dd>
                            <dd class="mt-1 break-words text-xs text-ui-subtext">
                                {{ $merchant->user->email }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                Contact
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $merchant->contact_number ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ route('admin.merchants.edit', $merchant) }}"
                       class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor sm:w-auto">
                        Edit Merchant
                    </a>
                </article>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="font-medium text-ui-text">
                        No merchant profiles yet.
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext">
                        Add accredited merchants before claim validation begins.
                    </p>

                    <a href="{{ route('admin.merchants.create') }}"
                       class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Add First Merchant
                    </a>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $merchants->firstItem() ?? 0 }} to {{ $merchants->lastItem() ?? 0 }} of {{ $merchants->total() }} merchants
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $merchants->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
