<?php

namespace App\Notifications;

use App\Actions\Webhooks\BuildWebhookPayload;
use App\Enums\NotificationEvent;
use App\Enums\NotificationLevel;
use App\Models\Result;

/**
 * A channel-agnostic notification, rendered once and delivered by each driver.
 */
final readonly class NotificationMessage
{
    public function __construct(
        public NotificationEvent $event,
        public Result $result,
        public string $title,
        public string $body,
        public NotificationLevel $level,
        public string $url,
        public ?string $speedtestUrl = null,
        public bool $test = false,
    ) {}

    /**
     * Get the body with the result links appended, for channels that can't
     * render their own buttons.
     */
    public function bodyWithLinks(): string
    {
        return rtrim($this->body)."\n\n".view('notifications.partials.links', [
            'speedtestUrl' => $this->speedtestUrl,
            'url' => $this->url,
        ])->render();
    }

    /**
     * Get the structured payload for machine consumers such as webhooks.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return BuildWebhookPayload::run($this->result, $this->event);
    }
}
