<?php

namespace App\Notifications\Apprise;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PeriodicAverageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $stats,
        public string $period,
        public string $periodLabel,
        public array $serverStats,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['apprise'];
    }

    /**
     * Get the Apprise message representation of the notification.
     */
    public function toApprise(object $notifiable): AppriseMessage
    {
        $body = view('apprise.periodic-average', [
            'stats' => $this->stats,
            'period' => $this->period,
            'periodLabel' => $this->periodLabel,
            'serverStats' => $this->serverStats,
        ])->render();

        return AppriseMessage::create()
            ->urls($notifiable->routes['apprise_urls'])
            ->title($this->period.' Speedtest Average Report')
            ->body($body)
            ->type('info');
    }
}
