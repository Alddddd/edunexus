<?php

use App\Http\Controllers\Admin\AssistanceProgramController;
use App\Http\Controllers\Admin\AssistanceRequestController;
use App\Http\Controllers\Admin\BlockchainTransactionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\SettlementController;
use App\Http\Controllers\Auditor\DashboardController as AuditorDashboardController;
use App\Http\Controllers\Member\AssistanceRequestController as MemberAssistanceRequestController;
use App\Http\Controllers\Member\ClaimController;
use App\Http\Controllers\Merchant\ClaimValidationController;
use App\Http\Controllers\ProfileController;
use App\Models\AssistanceRequest;
use App\Models\Settlement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
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

        Route::get('/admin/blockchain-transactions', [BlockchainTransactionController::class, 'index'])
            ->name('admin.blockchain-transactions.index');

        Route::post('/admin/blockchain-transactions/{blockchainTransaction}/confirm', [BlockchainTransactionController::class, 'confirm'])
            ->name('admin.blockchain-transactions.confirm');

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
                'approvedClaimPasses' => $memberRequests
                    ->where('status', 'Approved')
                    ->where('is_claimed', false)
                    ->count(),
                'claimedRequests' => $memberRequests->where('is_claimed', true)->count(),
                'latestRequest' => $memberRequests->first(),
                'latestApprovedClaimPass' => $memberRequests
                    ->where('status', 'Approved')
                    ->where('is_claimed', false)
                    ->first(),
                'recentClaims' => $memberRequests->take(5),
            ]);
        })->name('member.dashboard');

        Route::resource('/member/assistance-requests', MemberAssistanceRequestController::class)
            ->only(['create', 'store'])
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

            $merchantSettlements = Settlement::with(['assistanceRequest.member', 'assistanceRequest.program'])
                ->where('merchant_id', $merchantId)
                ->latest()
                ->get();

            return view('merchant.dashboard', [
                'merchantProfile' => $merchantProfile,
                'processedClaims' => $merchantSettlements->count(),
                'pendingSettlements' => $merchantSettlements->where('status', 'Pending')->count(),
                'settledSettlements' => $merchantSettlements->where('status', 'Settled')->count(),
                'pendingSettlementValue' => $merchantSettlements->where('status', 'Pending')->sum('amount'),
                'settledSettlementValue' => $merchantSettlements->where('status', 'Settled')->sum('amount'),
                'totalSettlementValue' => $merchantSettlements->sum('amount'),
                'recentSettlements' => $merchantSettlements->take(5),
            ]);
        })->name('merchant.dashboard');

        Route::get('/merchant/claims/validate', [ClaimValidationController::class, 'index'])
            ->name('merchant.claims.index');

        Route::match(['GET', 'POST'], '/merchant/claims/verify', [ClaimValidationController::class, 'verify'])
            ->name('merchant.claims.verify');

        Route::post('/merchant/claims/{assistanceRequest}/process', [ClaimValidationController::class, 'process'])
            ->name('merchant.claims.process');
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
