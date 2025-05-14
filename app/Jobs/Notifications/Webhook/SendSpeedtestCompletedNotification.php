<?php

namespace App\Jobs\Notifications\Webhook;

use App\Models\Result;
use App\Services\Notifications\SpeedtestNotificationData;
use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Handle the job.
     */
    public function handle(): void
    {
        $notificationSettings = new NotificationSettings;

        if (! count($notificationSettings->webhook_urls)) {
            Log::warning('Webhook URLs not found, check webhook notification channel settings.');

            return;
        }

        $data = SpeedtestNotificationData::make($this->result);

        foreach ($notificationSettings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload($data)
                ->doNotSign()
                ->dispatch();
        }
    }
}
