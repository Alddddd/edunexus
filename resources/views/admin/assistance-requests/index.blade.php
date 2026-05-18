@extends('layouts.dashboard')

@section('title', 'Assistance Requests')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Assistance Requests</h1>

        <p class="text-slate-500 mt-2">
            Review member assistance applications before approval and QR generation.
        </p>
    </div>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-semibold text-slate-700 mb-3">
        Workflow Progress
    </p>

    <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
        <span class="px-3 py-1 rounded-full bg-slate-100">Request</span>
        <span>→</span>
        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">Approval</span>
        <span>→</span>
        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">QR Generated</span>
        <span>→</span>
        <span class="px-3 py-1 rounded-full bg-cyan-100 text-cyan-700">Merchant Claim</span>
        <span>→</span>
        <span class="px-3 py-1 rounded-full bg-teal-100 text-teal-700">Blockchain Log</span>
    </div>
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
                    <th class="px-6 py-4 text-left">Member</th>
                    <th class="px-6 py-4 text-left">Program</th>
                    <th class="px-6 py-4 text-left">Requested Amount</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Action</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse($requests as $request)

                    <tr>

                        <td class="px-6 py-4 font-medium text-slate-700">
                            {{ $request->member->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $request->program->program_name }}
                        </td>

                        <td class="px-6 py-4 font-semibold">
                            ₱{{ number_format($request->requested_amount, 2) }}
                        </td>

                        <td class="px-6 py-4">

                           <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $request->status === 'Approved'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : ($request->status === 'Rejected'
                                        ? 'bg-red-100 text-red-700'
                                        : ($request->is_claimed
                                            ? 'bg-cyan-100 text-cyan-700'
                                            : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ $request->is_claimed ? 'Claimed' : $request->status }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-slate-500">
                            {{ $request->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4">

                            <a href="{{ route('admin.assistance-requests.show', $request) }}"
                               class="text-teal-600 font-medium hover:text-teal-700">

                                Review

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                            No assistance requests submitted yet.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-slate-500">
                    Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests
                </p>

                <div class="flex justify-center">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
