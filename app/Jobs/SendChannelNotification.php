<?php

namespace App\Jobs;

use App\Actions\Notifications\BuildNotificationMessage;
use App\Enums\NotificationEvent;
use App\Models\NotificationChannel;
use App\Models\Result;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendChannelNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Only try once, retrying could deliver duplicates to services that
     * accepted the message but responded with an error.
     */
    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public NotificationChannel $channel,
        public Result $result,
        public NotificationEvent $event,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $driver = $this->channel->type->driver();

        $driver->send($this->channel, BuildNotificationMessage::run($this->result, $this->event));

        if (! $driver->deliversAsynchronously()) {
            $this->channel->markAsSent();
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Notification channel delivery failed', [
            'channel' => $this->channel->id,
            'type' => $this->channel->type->value,
            'event' => $this->event->value,
            'result' => $this->result->id,
            'message' => $exception?->getMessage(),
        ]);

        $this->channel->markAsFailed($exception?->getMessage() ?? 'Unknown error');
    }
}
