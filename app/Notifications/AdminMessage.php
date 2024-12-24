<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminMessage extends Notification
{
    use Queueable;
    private $message;
    private $title;
    private $message_en;
    private $title_en;
    public $key;
    public $keyId;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $title, $message_en = '', $title_en = '', $key = null, $keyId = null)
    {
        $this->message = $message;
        $this->title = $title;
        $this->message_en = $message_en;
        $this->title_en = $title_en;
        $this->key = $key;
        $this->keyId = $keyId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => $this->message,
            'title' => $this->title,
            'message_en' => $this->message_en,
            'title_en' => $this->title_en,
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'key' => $this->key,
            'keyId' => $this->keyId,
        ];
    }
}
