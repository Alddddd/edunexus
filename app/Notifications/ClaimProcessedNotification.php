<?php

namespace App\Notifications;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClaimProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AssistanceRequest $assistanceRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Assistance Claim Processed',
            'message' => 'Your assistance claim '
                . $this->assistanceRequest->reference_code
                . ' has been successfully processed by the merchant.',

            'status' => 'Claimed',

            'action_url' => route(
                'member.claims.show',
                $this->assistanceRequest
            ),

            'reference_code' => $this->assistanceRequest->reference_code,
        ];
    }
}