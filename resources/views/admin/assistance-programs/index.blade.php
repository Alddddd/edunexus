@extends('layouts.dashboard')

@section('title', 'Assistance Programs')

@section('content')

<div class="flex items-center justify-between mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Assistance Programs
        </h1>

        <p class="text-slate-500 mt-2">
            Manage cooperative assistance programs and programmable claim rules.
        </p>
    </div>

    <a href="{{ route('admin.assistance-programs.create') }}"
       class="px-5 py-3 rounded-xl bg-teal-600 text-white font-medium hover:bg-teal-700 transition">

        Create Program

    </a>

</div>

@if(session('success'))

    <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700">
        {{ session('success') }}
    </div>

@endif

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

            <tr>

                <th class="px-6 py-4 text-left">Program</th>
                <th class="px-6 py-4 text-left">Merchant Category</th>
                <th class="px-6 py-4 text-left">Maximum Amount</th>
                <th class="px-6 py-4 text-left">Expiration</th>
                <th class="px-6 py-4 text-left">Status</th>

            </tr>

        </thead>

        <tbody class="divide-y divide-slate-100">

            @forelse($programs as $program)

                <tr>

                    <td class="px-6 py-4 font-medium text-slate-700">
                        {{ $program->program_name }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $program->merchant_category }}
                    </td>

                    <td class="px-6 py-4">
                        ₱{{ number_format($program->maximum_amount, 2) }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $program->expiration_days }} days
                    </td>

                    <td class="px-6 py-4">

                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $program->status === 'Active'
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-slate-200 text-slate-600' }}">

                            {{ $program->status }}

                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                        No assistance programs created yet.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
        <div class="flex flex-col items-center justify-center gap-3 text-center">
            <p class="text-sm text-slate-500">
                Showing {{ $programs->firstItem() ?? 0 }} to {{ $programs->lastItem() ?? 0 }} of {{ $programs->total() }} programs
            </p>

            <div class="flex justify-center">
                {{ $programs->links() }}
            </div>
        </div>
    </div>

</div>

@endsection
