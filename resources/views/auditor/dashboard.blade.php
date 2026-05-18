@extends('layouts.dashboard')

@section('title', 'Auditor Dashboard')

@section('content')

<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-800">
        Auditor Dashboard
    </h1>

    <p class="text-slate-500 mt-2">
        Monitor claim activity and blockchain verification records for audit transparency.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <p class="text-sm text-slate-500">Total Processed Claims</p>
        <h2 class="text-3xl font-bold text-slate-800 mt-3">{{ $totalClaims }}</h2>
        <p class="text-cyan-600 text-sm mt-2">Merchant-processed assistance</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <p class="text-sm text-slate-500">Confirmed Proofs</p>
        <h2 class="text-3xl font-bold text-slate-800 mt-3">{{ $confirmedProofs }}</h2>
        <p class="text-emerald-600 text-sm mt-2">Verified blockchain records</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <p class="text-sm text-slate-500">Pending Proofs</p>
        <h2 class="text-3xl font-bold text-slate-800 mt-3">{{ $pendingProofs }}</h2>
        <p class="text-yellow-600 text-sm mt-2">Awaiting confirmation</p>
    </div>

</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-800">
            Recent Blockchain Verification Records
        </h3>

        <p class="text-sm text-slate-500 mt-1">
            Latest proof logs created from claim processing activity.
        </p>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 text-left">Type</th>
                <th class="px-6 py-4 text-left">Reference</th>
                <th class="px-6 py-4 text-left">Hash</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Recorded</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($recentTransactions as $transaction)
                <tr>
                    <td class="px-6 py-4 font-medium text-slate-700">
                        {{ $transaction->transaction_type }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $transaction->reference_code ?? 'N/A' }}
                    </td>

                    <td class="px-6 py-4 font-mono text-xs text-slate-600">
                        {{ $transaction->transaction_hash ?? 'Pending' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $transaction->blockchain_status === 'Confirmed'
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $transaction->blockchain_status }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-slate-500">
                        {{ $transaction->recorded_at?->format('M d, Y h:i A') ?? 'Not recorded' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                        No verification records yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection