<?php

namespace App\Jobs\Notifications\Apprise;

use App\Models\Result;
use App\Services\Notifications\AppriseService;
use App\Services\Notifications\SpeedtestNotificationData;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    public function handle(): void
    {
        $settings = app(NotificationSettings::class);

        // If apprise_channel_urls is empty or not an array, skip
        if (empty($settings->apprise_channel_urls) || ! is_array($settings->apprise_channel_urls)) {
            Log::warning('Apprise service URLs not found; check Apprise notification settings.');

            return;
        }

        // Build the completed‐speedtest payload
        $data = SpeedtestNotificationData::make($this->result);

        $payloadBody = view('apprise.speedtest-completed', $data)->render();

        $payload = [
            'body' => $payloadBody,
            'title' => 'Speedtest Completed – #'.$this->result->id,
            'type' => 'info',
        ];

        // Send it!
        AppriseService::send($payload);
    }
}
