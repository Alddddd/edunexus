<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\AssistanceRequestApprovedNotification;
use App\Notifications\AssistanceRequestRejectedNotification;

class AssistanceRequestController extends Controller
{
    public function index()
    {
        $requests = AssistanceRequest::with(['member', 'program'])
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.assistance-requests.index', compact('requests'));
    }

    public function show(AssistanceRequest $assistanceRequest)
    {
        $assistanceRequest->load(['member', 'program']);

        return view('admin.assistance-requests.show', [
            'request' => $assistanceRequest,
        ]);
    }

   public function approve(Request $request, AssistanceRequest $assistanceRequest)
{
    if ($assistanceRequest->status !== 'Pending') {
        return back()->with('success', 'This request has already been reviewed.');
    }

    $assistanceRequest->loadMissing('program');

    $approvalLimit = min(
        (float) $assistanceRequest->program->maximum_amount,
        (float) $assistanceRequest->requested_amount
    );

    $validated = $request->validate([
        'approved_amount' => ['required', 'numeric', 'min:1', 'max:' . $approvalLimit],
    ], [
        'approved_amount.max' => 'The approved amount cannot exceed the lower of the requested amount and program maximum. Current limit: PHP ' . number_format($approvalLimit, 2) . '.',
    ]);

    $referenceCode = 'EDU-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

    $qrPayload = json_encode([
        'reference_code' => $referenceCode,
        'request_id' => $assistanceRequest->id,
        'member_id' => $assistanceRequest->member_id,
        'program_id' => $assistanceRequest->program_id,
        'status' => 'Approved',
    ]);

    $assistanceRequest->update([
        'approved_amount' => $validated['approved_amount'],
        'status' => 'Approved',
        'approval_date' => now(),
        'expiration_date' => now()->addDays($assistanceRequest->program->expiration_days),
        'reference_code' => $referenceCode,
        'qr_code' => $qrPayload,
        'approved_by' => auth()->id(),
    ]);

    $assistanceRequest->member->notify(
        new AssistanceRequestApprovedNotification($assistanceRequest)
    );

    ActivityLogService::record(
        'request_approved',
        'Assistance request approved',
        'Admin approved assistance request ' . $referenceCode . ' for ' . $assistanceRequest->member->name . '.',
        AssistanceRequest::class,
        $assistanceRequest->id,
        'Approved'
    );

    return redirect()
        ->route('admin.assistance-requests.index')
        ->with('success', 'Assistance request approved and QR claim generated successfully.');
}

public function reject(AssistanceRequest $assistanceRequest)
{
    if ($assistanceRequest->status !== 'Pending') {
        return back()->with('success', 'This request has already been reviewed.');
    }

    $assistanceRequest->update([
        'status' => 'Rejected',
        'approved_by' => auth()->id(),
    ]);

    $assistanceRequest->member->notify(
        new AssistanceRequestRejectedNotification($assistanceRequest)
    );

    ActivityLogService::record(
        'request_rejected',
        'Assistance request rejected',
        'Admin rejected an assistance request from ' . $assistanceRequest->member->name . '.',
        AssistanceRequest::class,
        $assistanceRequest->id,
        'Rejected'
    );

    return redirect()
        ->route('admin.assistance-requests.index')
        ->with('success', 'Assistance request rejected.');
}

}
