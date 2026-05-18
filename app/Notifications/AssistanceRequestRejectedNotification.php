<?php

namespace App\Notifications;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssistanceRequestRejectedNotification extends Notification
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
            'title' => 'Assistance Request Rejected',
            'message' => 'Your assistance request for '
                . $this->assistanceRequest->program->program_name
                . ' was not approved by the cooperative.',

            'status' => 'Rejected',

            'action_url' => route(
                'member.assistance-requests.create'
            ),

            'reference_code' => $this->assistanceRequest->reference_code,
        ];
    }
}