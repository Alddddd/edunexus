@extends('layouts.dashboard')

@section('title', 'Merchant Categories')

@section('content')
<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Merchant Categories"
        eyebrow="Operational Classifications"
        description="Manage reusable merchant categories used by assistance programs and accredited merchants.">
        <x-slot:actions>
            <a href="{{ route('admin.merchant-categories.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                Create Category
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Category Registry"
        description="Categories define which accredited merchants can validate each assistance program.">
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Category</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Usage</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($categories as $category)
                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">{{ $category->name }}</p>
                                <p class="mt-1 max-w-xl text-sm leading-5 text-ui-subtext">{{ $category->description ?: 'Reusable validation category.' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <p>{{ number_format($category->merchants_count) }} merchants</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ number_format($category->assistance_programs_count) }} programs</p>
                            </td>
                            <td class="px-5 py-4">
                                <x-status-badge :status="$category->status" :tone="$category->status === 'Active' ? 'active' : 'neutral'" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.merchant-categories.edit', $category) }}"
                                       class="inline-flex min-h-10 items-center rounded-xl border border-ui-border bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.merchant-categories.destroy', $category) }}"
                                          data-confirm
                                          data-confirm-title="Delete merchant category?"
                                          data-confirm-message="Only unused categories can be deleted. Existing merchant and program links are protected."
                                          data-confirm-button="Delete category"
                                          data-confirm-tone="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex min-h-10 items-center rounded-xl border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="font-medium text-ui-text">No merchant categories yet.</p>
                                <p class="mt-1 text-sm text-ui-subtext">Create categories such as School Supplies, Pharmacy, or Groceries.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80 md:hidden">
            @forelse($categories as $category)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-words font-semibold text-ui-text">{{ $category->name }}</p>
                            <p class="mt-1 text-sm leading-5 text-ui-subtext">{{ $category->description ?: 'Reusable validation category.' }}</p>
                        </div>
                        <x-status-badge :status="$category->status" :tone="$category->status === 'Active' ? 'active' : 'neutral'" size="xs" />
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Merchants</p>
                            <p class="mt-1 font-semibold text-ui-text">{{ number_format($category->merchants_count) }}</p>
                        </div>
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Programs</p>
                            <p class="mt-1 font-semibold text-ui-text">{{ number_format($category->assistance_programs_count) }}</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.merchant-categories.edit', $category) }}"
                       class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                        Edit Category
                    </a>
                </article>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="font-medium text-ui-text">No merchant categories yet.</p>
                    <p class="mt-1 text-sm text-ui-subtext">Create reusable classifications for merchant validation.</p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
                </p>
                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $categories->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
