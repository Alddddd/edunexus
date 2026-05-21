<?php

use App\Http\Controllers\Admin\AssistanceProgramController;
use App\Http\Controllers\Admin\AssistanceRequestController;
use App\Http\Controllers\Admin\BlockchainTransactionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MerchantUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettlementController;
use App\Http\Controllers\Auditor\DashboardController as AuditorDashboardController;
use App\Http\Controllers\Member\AssistanceRequestController as MemberAssistanceRequestController;
use App\Http\Controllers\Member\ClaimController;
use App\Http\Controllers\Merchant\ClaimValidationController;
use App\Http\Controllers\Merchant\PayoutSettingsController;
use App\Http\Controllers\ProfileController;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'member' => redirect()->route('member.dashboard'),
            'merchant' => redirect()->route('merchant.dashboard'),
            'auditor' => redirect()->route('auditor.dashboard'),
            default => abort(403, 'Invalid user role.'),
        };
    })->name('dashboard');

 Route::get('/notifications', [NotificationController::class, 'index'])
    ->name('notifications.index');

Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])
    ->name('notifications.read');

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
    ->name('notifications.mark-all-read');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::resource('/admin/assistance-programs', AssistanceProgramController::class)
            ->names('admin.assistance-programs');

        Route::resource('/admin/assistance-requests', AssistanceRequestController::class)
            ->only(['index', 'show'])
            ->names('admin.assistance-requests');

        Route::post('/admin/assistance-requests/{assistanceRequest}/approve', [AssistanceRequestController::class, 'approve'])
            ->name('admin.assistance-requests.approve');

        Route::post('/admin/assistance-requests/{assistanceRequest}/reject', [AssistanceRequestController::class, 'reject'])
            ->name('admin.assistance-requests.reject');

        Route::resource('/admin/merchants', MerchantController::class)
            ->only(['index', 'create', 'store', 'edit', 'update'])
            ->names('admin.merchants');

        Route::get('/admin/merchant-users', [MerchantUserController::class, 'index'])
            ->name('admin.merchant-users.index');

        Route::get('/admin/merchant-users/create', [MerchantUserController::class, 'create'])
            ->name('admin.merchant-users.create');

        Route::post('/admin/merchant-users', [MerchantUserController::class, 'store'])
            ->name('admin.merchant-users.store');

        Route::get('/admin/blockchain-transactions', [BlockchainTransactionController::class, 'index'])
            ->name('admin.blockchain-transactions.index');

        Route::post('/admin/blockchain-transactions/{blockchainTransaction}/confirm', [BlockchainTransactionController::class, 'confirm'])
            ->name('admin.blockchain-transactions.confirm');

        Route::get('/admin/reports', [ReportController::class, 'index'])
            ->name('admin.reports.index');

        Route::get('/admin/reports/exports/settlements', [ReportController::class, 'exportSettlements'])
            ->name('admin.reports.exports.settlements');

        Route::get('/admin/reports/exports/proofs', [ReportController::class, 'exportProofs'])
            ->name('admin.reports.exports.proofs');

        Route::get('/admin/settlements', [SettlementController::class, 'index'])
            ->name('admin.settlements.index');

        Route::post('/admin/settlements/{settlement}/settle', [SettlementController::class, 'markAsSettled'])
            ->name('admin.settlements.settle');

            Route::get('/admin/activity-logs', [ActivityLogController::class, 'index'])
    ->name('admin.activity-logs.index');
    });

    Route::middleware('role:member')->group(function () {
        Route::get('/member/dashboard', function () {
            $memberId = auth()->id();
            $memberRequests = AssistanceRequest::with('program')
                ->where('member_id', $memberId)
                ->latest()
                ->get();

            return view('member.dashboard', [
                'totalRequests' => $memberRequests->count(),
                'pendingRequests' => $memberRequests->where('status', 'Pending')->count(),
                'approvedRequests' => $memberRequests->where('status', 'Approved')->count(),
                'approvedClaimPasses' => $memberRequests
                    ->where('status', 'Approved')
                    ->where('is_claimed', false)
                    ->count(),
                'claimedRequests' => $memberRequests->where('is_claimed', true)->count(),
                'rejectedRequests' => $memberRequests->where('status', 'Rejected')->count(),
                'latestRequest' => $memberRequests->first(),
                'latestApprovedClaimPass' => $memberRequests
                    ->where('status', 'Approved')
                    ->where('is_claimed', false)
                    ->first(),
                'recentClaims' => $memberRequests->take(5),
                'recentNotifications' => auth()->user()->notifications()->latest()->take(5)->get(),
                'unreadNotifications' => auth()->user()->unreadNotifications()->count(),
            ]);
        })->name('member.dashboard');

        Route::resource('/member/assistance-requests', MemberAssistanceRequestController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->names('member.assistance-requests');

        Route::get('/member/claims', [ClaimController::class, 'index'])
            ->name('member.claims.index');

        Route::get('/member/claims/{assistanceRequest}', [ClaimController::class, 'show'])
            ->name('member.claims.show');
    });

    Route::middleware('role:merchant')->group(function () {
        Route::get('/merchant/dashboard', function () {
            $merchantId = auth()->id();
            $merchantProfile = auth()->user()->merchantProfile;

            $merchantSettlements = Settlement::with(['assistanceRequest.member', 'assistanceRequest.program', 'payouts'])
                ->where('merchant_id', $merchantId)
                ->latest()
                ->get();

            $pendingValidations = collect();

            if ($merchantProfile && $merchantProfile->status === 'Active') {
                $pendingValidations = AssistanceRequest::with(['member', 'program'])
                    ->where('status', 'Approved')
                    ->where('is_claimed', false)
                    ->whereHas('program', function ($query) use ($merchantProfile) {
                        $query->where('merchant_category', $merchantProfile->merchant_category);
                    })
                    ->latest()
                    ->take(5)
                    ->get();
            }

            $recentRequestIds = $merchantSettlements
                ->pluck('assistance_request_id')
                ->filter()
                ->unique()
                ->values();

            $morphProofConfirmations = BlockchainTransaction::where('transaction_type', 'Claim')
                ->whereIn('reference_id', $recentRequestIds)
                ->where('blockchain_status', 'Confirmed')
                ->count();

            return view('merchant.dashboard', [
                'merchantProfile' => $merchantProfile,
                'processedClaims' => $merchantSettlements->count(),
                'pendingSettlements' => $merchantSettlements->whereIn('status', ['Pending', 'Partially Released'])->count(),
                'settledSettlements' => $merchantSettlements->whereIn('status', ['Released', 'Settled'])->count(),
                'pendingSettlementValue' => $merchantSettlements->whereIn('status', ['Pending', 'Partially Released'])->sum('remaining_balance'),
                'settledSettlementValue' => $merchantSettlements->sum('total_released'),
                'totalSettlementValue' => $merchantSettlements->sum('amount'),
                'recentSettlements' => $merchantSettlements->take(5),
                'pendingValidationCount' => $pendingValidations->count(),
                'pendingValidations' => $pendingValidations,
                'morphProofConfirmations' => $morphProofConfirmations,
            ]);
        })->name('merchant.dashboard');

        Route::get('/merchant/settlements', function () {
            $merchantId = auth()->id();

            $settlements = Settlement::with([
                    'assistanceRequest.member',
                    'assistanceRequest.program',
                    'payouts' => fn ($query) => $query->latest('released_at')->latest('id'),
                ])
                ->where('merchant_id', $merchantId)
                ->latest()
                ->paginate(8)
                ->withQueryString();

            $requestIds = $settlements->pluck('assistance_request_id')->filter();

            $proofRecords = BlockchainTransaction::query()
                ->where('transaction_type', 'Settlement')
                ->whereIn('reference_id', $requestIds)
                ->latest('recorded_at')
                ->latest('id')
                ->get()
                ->unique('reference_id')
                ->keyBy('reference_id');

            return view('merchant.settlements.index', compact('settlements', 'proofRecords'));
        })->name('merchant.settlements.index');

        Route::get('/merchant/claims/validate', [ClaimValidationController::class, 'index'])
            ->name('merchant.claims.index');

        Route::match(['GET', 'POST'], '/merchant/claims/verify', [ClaimValidationController::class, 'verify'])
            ->name('merchant.claims.verify');

        Route::post('/merchant/claims/{assistanceRequest}/process', [ClaimValidationController::class, 'process'])
            ->name('merchant.claims.process');

        Route::get('/merchant/payout-settings', [PayoutSettingsController::class, 'edit'])
            ->name('merchant.payout-settings.edit');

        Route::put('/merchant/payout-settings', [PayoutSettingsController::class, 'update'])
            ->name('merchant.payout-settings.update');
    });

    Route::middleware('role:auditor')->group(function () {
        Route::get('/auditor/dashboard', [AuditorDashboardController::class, 'index'])
            ->name('auditor.dashboard');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
