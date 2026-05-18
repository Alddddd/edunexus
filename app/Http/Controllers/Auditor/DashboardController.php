<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        return view('auditor.dashboard', [
            'totalClaims' => AssistanceRequest::where('is_claimed', true)->count(),
            'confirmedProofs' => BlockchainTransaction::where('blockchain_status', 'Confirmed')->count(),
            'pendingProofs' => BlockchainTransaction::where('blockchain_status', 'Pending')->count(),
            'recentTransactions' => BlockchainTransaction::latest()->take(10)->get(),
        ]);
    }
}