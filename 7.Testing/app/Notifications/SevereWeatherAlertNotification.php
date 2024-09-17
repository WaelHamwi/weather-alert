<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SevereWeatherAlertNotification extends Notification
{
    protected $message;
    protected $url;

    public function __construct($message, $url)
    {
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Severe Weather Alert')
            ->line($this->message)
            ->action('View Details', $this->url)
            ->line('Stay safe and be prepared.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
    // Optionally: Add more notification channels (like database or SMS)
}
