<?php

namespace App\Notifications;

use App\Models\AssistanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssistanceRequestApprovedNotification extends Notification
{
    use Queueable;

    protected AssistanceRequest $assistanceRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssistanceRequest $assistanceRequest)
    {
        $this->assistanceRequest = $assistanceRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_approved',

            'title' => 'Assistance Request Approved',

            'message' => 'Your assistance request for '
                . $this->assistanceRequest->program->program_name
                . ' has been approved.',

            'reference_code' => $this->assistanceRequest->reference_code,

            'status' => 'Approved',

            'action_url' => route(
                'member.claims.show',
                $this->assistanceRequest
            ),
        ];
    }
}