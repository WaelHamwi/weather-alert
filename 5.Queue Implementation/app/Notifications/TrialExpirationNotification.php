<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TrialExpirationNotification extends Notification
{
    private $message;
    private $actionUrl;

    public function __construct($message, $actionUrl)
    {
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; 
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ]);
    }
}
