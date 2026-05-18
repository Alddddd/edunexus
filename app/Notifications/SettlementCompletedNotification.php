<?php

namespace App\Notifications;

use App\Models\Settlement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SettlementCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Settlement $settlement
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Settlement Completed',

            'message' => 'Your merchant settlement for reference '
                . $this->settlement->assistanceRequest->reference_code
                . ' has been marked as settled by the cooperative.',

            'status' => 'Settled',

            'action_url' => route(
                'merchant.dashboard'
            ),

            'reference_code' => $this->settlement->assistanceRequest->reference_code,
        ];
    }
}