<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRequests = AssistanceRequest::count();
        $approvedRequests = AssistanceRequest::where('status', 'Approved')->count();
        $rejectedRequests = AssistanceRequest::where('status', 'Rejected')->count();

        $topProgram = AssistanceRequest::with('program')
            ->selectRaw('program_id, COUNT(*) as total')
            ->groupBy('program_id')
            ->orderByDesc('total')
            ->first();

        return view('admin.dashboard', [
            'totalApprovedAssistance' => AssistanceRequest::where('status', 'Approved')->sum('approved_amount'),
            'pendingRequests' => AssistanceRequest::where('status', 'Pending')->count(),
            'claimedRequests' => AssistanceRequest::where('is_claimed', true)->count(),
            'confirmedBlockchainLogs' => BlockchainTransaction::where('blockchain_status', 'Confirmed')->count(),

            'pendingSettlements' => Settlement::where('status', 'Pending')->count(),
            'settledAmount' => Settlement::where('status', 'Settled')->sum('amount'),
            'pendingBlockchainProofs' => BlockchainTransaction::where('blockchain_status', 'Pending')->count(),
            'topProgramName' => $topProgram?->program?->program_name ?? 'No activity yet',
            'totalRequests' => $totalRequests,
            'approvedRequests' => $approvedRequests,
            'rejectedRequests' => $rejectedRequests,
            'approvalRate' => $totalRequests > 0
                ? round(($approvedRequests / $totalRequests) * 100, 1)
                : 0,
            'pendingSettlementAmount' => Settlement::where('status', 'Pending')->sum('amount'),
            'totalSettlements' => Settlement::count(),
            'latestBlockchainTransaction' => BlockchainTransaction::latest()->first(),

            'latestPendingSettlements' => Settlement::with([
                    'assistanceRequest.member',
                    'merchant.merchantProfile',
                ])
                ->where('status', 'Pending')
                ->latest()
                ->take(5)
                ->get(),

            'latestPendingRequests' => AssistanceRequest::with(['member', 'program'])
                ->where('status', 'Pending')
                ->latest()
                ->take(5)
                ->get(),

            'recentRequests' => AssistanceRequest::with(['member', 'program'])
                ->latest()
                ->take(5)
                ->get(),

            'recentActivities' => ActivityLog::with('user')
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
