<?php

namespace App\Notifications;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssistanceRequestSubmittedNotification extends Notification
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
            'type' => 'request_submitted',
            'title' => 'New Assistance Request',
            'message' => $this->assistanceRequest->member->name
                . ' submitted a request for '
                . $this->assistanceRequest->program->program_name
                . '.',
            'status' => 'Pending',
            'action_url' => route('admin.assistance-requests.show', $this->assistanceRequest),
            'reference_code' => $this->assistanceRequest->reference_code,
        ];
    }
}
