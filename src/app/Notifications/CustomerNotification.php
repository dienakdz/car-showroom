<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomerNotification extends Notification
{
    use Queueable;

    /**
     * @param array{
     *     category: string,
     *     title: string,
     *     message: string,
     *     action_url?: string|null,
     *     icon?: string|null,
     *     meta?: array<string, mixed>
     * } $payload
     */
    public function __construct(public array $payload) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload;
    }
}
