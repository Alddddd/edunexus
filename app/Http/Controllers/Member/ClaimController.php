<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;

class ClaimController extends Controller
{
    public function index()
    {
        $claimStats = [
            'total' => AssistanceRequest::where('member_id', auth()->id())->count(),
            'pending' => AssistanceRequest::where('member_id', auth()->id())
                ->where('status', 'Pending')
                ->count(),
            'active' => AssistanceRequest::where('member_id', auth()->id())
                ->where('status', 'Approved')
                ->where('is_claimed', false)
                ->count(),
            'claimed' => AssistanceRequest::where('member_id', auth()->id())
                ->where('is_claimed', true)
                ->count(),
        ];

        $claims = AssistanceRequest::with('program')
            ->where('member_id', auth()->id())
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('member.claims.index', compact('claims', 'claimStats'));
    }

    public function show(AssistanceRequest $assistanceRequest)
    {
        abort_if($assistanceRequest->member_id !== auth()->id(), 403);

        $assistanceRequest->load('program');

        return view('member.claims.show', [
            'claim' => $assistanceRequest,
        ]);
    }
}
