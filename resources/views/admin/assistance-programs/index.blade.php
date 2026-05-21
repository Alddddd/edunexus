@extends('layouts.dashboard')

@section('title', 'Assistance Programs')

@section('content')
<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Assistance Programs"
        eyebrow="Program Rules"
        description="Manage cooperative assistance programs and programmable claim rules.">
        <x-slot:actions>
            <a href="{{ route('admin.assistance-programs.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                Create Program
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Program Registry"
        description="Program limits, merchant categories, and expiration windows define how member claim passes can be validated.">
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Program</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Merchant Category</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Maximum Amount</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Expiration</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($programs as $program)
                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">
                                    {{ $program->program_name }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <x-status-badge :status="$program->category?->name ?? $program->merchant_category" tone="proof" />
                            </td>

                            <td class="px-5 py-4 font-semibold text-ui-text">
                                &#8369;{{ number_format($program->maximum_amount, 2) }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $program->expiration_days }} days
                            </td>

                            <td class="px-5 py-4">
                                <x-status-badge :status="$program->status" :tone="$program->status === 'Active' ? 'active' : 'neutral'" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.assistance-programs.edit', $program) }}"
                                   class="inline-flex min-h-10 items-center rounded-xl border border-ui-border bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-ui-text">
                                    No assistance programs created yet.
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    Create a program to define request limits and claim validation rules.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80 md:hidden">
            @forelse($programs as $program)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-ui-text">
                                {{ $program->program_name }}
                            </p>

                            <div class="mt-2 flex flex-wrap gap-2">
                                <x-status-badge :status="$program->category?->name ?? $program->merchant_category" tone="proof" />
                                <x-status-badge :status="$program->status" :tone="$program->status === 'Active' ? 'active' : 'neutral'" />
                            </div>
                        </div>
                        <a href="{{ route('admin.assistance-programs.edit', $program) }}"
                           class="shrink-0 rounded-xl border border-ui-border bg-white px-3 py-2 text-xs font-semibold text-slate-700">
                            Edit
                        </a>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                Maximum
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                &#8369;{{ number_format($program->maximum_amount, 2) }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                Expiration
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $program->expiration_days }} days
                            </dd>
                        </div>
                    </dl>
                </article>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="font-medium text-ui-text">
                        No assistance programs created yet.
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext">
                        Create a program to define request limits and claim validation rules.
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $programs->firstItem() ?? 0 }} to {{ $programs->lastItem() ?? 0 }} of {{ $programs->total() }} programs
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $programs->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
