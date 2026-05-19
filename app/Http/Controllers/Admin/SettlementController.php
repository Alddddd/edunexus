<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use App\Services\ActivityLogService;
use App\Notifications\SettlementCompletedNotification;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'in:Pending,Settled'],
        ]);

        $stats = [
            'total' => Settlement::count(),
            'pending' => Settlement::where('status', 'Pending')->count(),
            'settled' => Settlement::where('status', 'Settled')->count(),
            'released' => Settlement::where('status', 'Settled')->count(),
            'pending_amount' => Settlement::where('status', 'Pending')->sum('amount'),
            'settled_amount' => Settlement::where('status', 'Settled')->sum('amount'),
            'released_amount' => Settlement::where('status', 'Settled')->sum('amount'),
            'total_amount' => Settlement::sum('amount'),
        ];

        $settlements = Settlement::with([
                'assistanceRequest.member',
                'assistanceRequest.program',
                'merchant.merchantProfile',
            ])
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        $proofRecords = BlockchainTransaction::query()
            ->where('transaction_type', 'Claim')
            ->whereIn('reference_id', $settlements->pluck('assistance_request_id')->filter())
            ->latest('recorded_at')
            ->latest('id')
            ->get()
            ->unique('reference_id')
            ->keyBy('reference_id');

        return view('admin.settlements.index', compact(
            'settlements',
            'filters',
            'stats',
            'proofRecords'
        ));
    }

 public function markAsSettled(Settlement $settlement)
{
    if ($settlement->status === 'Settled') {
        return back()->with('success', 'Settlement has already been released.');
    }

    $settlement->update([
        'status' => 'Settled',
        'settled_at' => now(),
    ]);

    $settlement->merchant->notify(
        new SettlementCompletedNotification($settlement)
    );

    ActivityLogService::record(
        'settlement_completed',
        'Merchant settlement released',
        'Admin released merchant settlement #' . $settlement->id . '.',
        \App\Models\Settlement::class,
        $settlement->id,
        'Settled'
    );

    return back()->with('success', 'Settlement released and merchant notified.');
}

    
}
