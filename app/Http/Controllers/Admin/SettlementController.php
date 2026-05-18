<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'pending_amount' => Settlement::where('status', 'Pending')->sum('amount'),
            'settled_amount' => Settlement::where('status', 'Settled')->sum('amount'),
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

        return view('admin.settlements.index', compact(
            'settlements',
            'filters',
            'stats'
        ));
    }

 public function markAsSettled(Settlement $settlement)
{
    if ($settlement->status === 'Settled') {
        return back()->with('success', 'Settlement is already marked as settled.');
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
        'Merchant settlement completed',
        'Admin marked settlement #' . $settlement->id . ' as settled.',
        \App\Models\Settlement::class,
        $settlement->id,
        'Settled'
    );

    return back()->with('success', 'Settlement marked as settled.');
}

    
}
