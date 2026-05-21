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
            'title' => 'Payout Released',

            'message' => 'PHP payout released for merchant settlement reference '
                . $this->settlement->assistanceRequest->reference_code
                . '. Remaining balance: PHP '
                . number_format((float) $this->settlement->remaining_balance, 2) . '.',

            'status' => $this->settlement->status,

            'action_url' => route(
                'merchant.dashboard'
            ),

            'reference_code' => $this->settlement->assistanceRequest->reference_code,
        ];
    }
}
