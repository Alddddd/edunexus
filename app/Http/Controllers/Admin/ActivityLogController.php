<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'max:255'],
            'event_type' => ['nullable', 'string', 'max:255'],
        ]);

        $statusOptions = ActivityLog::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $eventTypeOptions = ActivityLog::query()
            ->whereNotNull('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type');

        $stats = [
            'total' => ActivityLog::count(),
            'approvals' => ActivityLog::whereIn('event_type', ['request_approved', 'request_rejected'])->count(),
            'claims' => ActivityLog::where('event_type', 'claim_processed')->count(),
            'settlements' => ActivityLog::where('event_type', 'settlement_completed')->count(),
            'attention' => ActivityLog::whereIn('status', ['Rejected', 'Failed'])->count(),
        ];

        $activitiesQuery = ActivityLog::with('user')
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['event_type'] ?? null, function ($query, $eventType) {
                $query->where('event_type', $eventType);
            })
            ->latest()
            ->latest('id');

        $activities = $activitiesQuery
            ->paginate(5)
            ->withQueryString();

        return view('admin.activity-logs.index', compact(
            'activities',
            'filters',
            'statusOptions',
            'eventTypeOptions',
            'stats'
        ));
    }
}
