<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AssistanceRequest;
use App\Notifications\AssistanceRequestApprovedNotification;
use App\Notifications\AssistanceRequestRejectedNotification;
use App\Notifications\AssistanceRequestSubmittedNotification;
use App\Notifications\ClaimProcessedNotification;
use Illuminate\Database\Seeder;

class AssistanceRequestSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        $admin = $this->demoMember('admin@edunexus.test');
        $education = $this->demoProgram('Education Assistance');
        $emergency = $this->demoProgram('Emergency Assistance');
        $medical = $this->demoProgram('Medical Assistance');

        $ana = $this->demoMember('ana.reyes@edunexus.test');
        $roberto = $this->demoMember('roberto.cruz@edunexus.test');
        $lorna = $this->demoMember('lorna.garcia@edunexus.test');
        $maria = $this->demoMember('maria.santos@edunexus.test');

        $lipaMerchant = $this->demoMerchant('lipa.supplies@edunexus.test');
        $educareMerchant = $this->demoMerchant('educare.bookstore@edunexus.test');
        $pharmacyMerchant = $this->demoMerchant('health.pharmacy@edunexus.test');

        $this->pendingRequest($maria, $education, 3200, 'Classroom paper, bond paper, and visual aid materials for Grade 4 modules.', now()->subHours(10));

        $this->rejectedRequest($lorna, $emergency, $admin, 9200, 'Requested amount exceeds the configured emergency assistance ceiling for this demo cycle.', now()->subDays(4));

        $this->approvedRequest(
            'EDU-DEMO-QR-001',
            $roberto,
            $education,
            $admin,
            2800,
            'Approved unclaimed QR pass for immediate merchant validation demo.',
            now()->subDays(2),
            null
        );

        $this->approvedRequest(
            'EDU-DEMO-CLAIM-001',
            $ana,
            $education,
            $admin,
            4200,
            'Claimed classroom supplies purchase awaiting settlement release.',
            now()->subDays(5),
            $lipaMerchant,
            now()->subDays(3)
        );

        $this->approvedRequest(
            'EDU-DEMO-PARTIAL-001',
            $maria,
            $education,
            $admin,
            5000,
            'Partially released bookstore reimbursement for teacher learning kits.',
            now()->subDays(8),
            $educareMerchant,
            now()->subDays(6)
        );

        $this->approvedRequest(
            'EDU-DEMO-RELEASED-001',
            $lorna,
            $medical,
            $admin,
            6500,
            'Fully released pharmacy assistance payout with Morph and EDUX proof metadata.',
            now()->subDays(11),
            $pharmacyMerchant,
            now()->subDays(9)
        );
    }

    private function pendingRequest($member, $program, float $amount, string $reason, $createdAt): AssistanceRequest
    {
        $request = AssistanceRequest::updateOrCreate(
            [
                'member_id' => $member->id,
                'program_id' => $program->id,
                'reason' => $reason,
            ],
            [
                'requested_amount' => $amount,
                'approved_amount' => null,
                'status' => 'Pending',
                'approval_date' => null,
                'expiration_date' => null,
                'reference_code' => null,
                'qr_code' => null,
                'approved_by' => null,
                'is_claimed' => false,
                'claimed_at' => null,
                'claimed_by' => null,
                'claim_status' => 'Unclaimed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );

        $this->notifyAdmins($request, AssistanceRequestSubmittedNotification::class);
        $this->activity('request_submitted', 'Member assistance request submitted', $member->name . ' submitted a request for ' . $program->program_name . '.', AssistanceRequest::class, $request->id, 'Pending', $member->id, $createdAt);

        return $request;
    }

    private function rejectedRequest($member, $program, $admin, float $amount, string $reason, $createdAt): AssistanceRequest
    {
        $request = AssistanceRequest::updateOrCreate(
            [
                'member_id' => $member->id,
                'program_id' => $program->id,
                'reason' => $reason,
            ],
            [
                'requested_amount' => $amount,
                'approved_amount' => null,
                'status' => 'Rejected',
                'approval_date' => $createdAt->copy()->addHours(4),
                'expiration_date' => null,
                'reference_code' => null,
                'qr_code' => null,
                'approved_by' => $admin->id,
                'is_claimed' => false,
                'claimed_at' => null,
                'claimed_by' => null,
                'claim_status' => 'Rejected',
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(4),
            ]
        );

        $member->notify(new AssistanceRequestRejectedNotification($request));
        $this->activity('request_rejected', 'Assistance request rejected', 'Admin rejected an assistance request from ' . $member->name . '.', AssistanceRequest::class, $request->id, 'Rejected', $admin->id, $createdAt->copy()->addHours(4));

        return $request;
    }

    private function approvedRequest(
        string $referenceCode,
        $member,
        $program,
        $admin,
        float $amount,
        string $reason,
        $approvedAt,
        $merchant = null,
        $claimedAt = null
    ): AssistanceRequest {
        $request = AssistanceRequest::updateOrCreate(
            ['reference_code' => $referenceCode],
            [
                'member_id' => $member->id,
                'program_id' => $program->id,
                'requested_amount' => $amount,
                'approved_amount' => $amount,
                'status' => 'Approved',
                'approval_date' => $approvedAt,
                'expiration_date' => $approvedAt->copy()->addDays((int) $program->expiration_days),
                'approved_by' => $admin->id,
                'reason' => $reason,
                'is_claimed' => filled($claimedAt),
                'claimed_at' => $claimedAt,
                'claimed_by' => $merchant?->id,
                'claim_status' => filled($claimedAt) ? 'Claimed' : 'Unclaimed',
                'created_at' => $approvedAt->copy()->subHours(8),
                'updated_at' => $claimedAt ?? $approvedAt,
            ]
        );

        $request->update(['qr_code' => $this->qrPayload($request)]);

        $member->notify(new AssistanceRequestApprovedNotification($request));
        $this->activity('request_approved', 'Assistance request approved', 'Admin approved assistance request ' . $referenceCode . ' for ' . $member->name . '.', AssistanceRequest::class, $request->id, 'Approved', $admin->id, $approvedAt);

        if ($claimedAt) {
            $member->notify(new ClaimProcessedNotification($request));
            $this->activity('claim_processed', 'Merchant processed assistance claim', ($merchant->merchantProfile->business_name ?? $merchant->name) . ' validated and processed claim ' . $referenceCode . '.', AssistanceRequest::class, $request->id, 'Confirmed', $merchant->id, $claimedAt);
        }

        return $request;
    }

    private function notifyAdmins(AssistanceRequest $request, string $notificationClass): void
    {
        $admins = \App\Models\User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new $notificationClass($request));
        }
    }

    private function activity(string $eventType, string $title, string $description, string $referenceType, int $referenceId, string $status, int $userId, $createdAt): void
    {
        ActivityLog::updateOrCreate(
            [
                'event_type' => $eventType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ],
            [
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );
    }
}
