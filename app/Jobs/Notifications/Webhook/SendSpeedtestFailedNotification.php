<?php

namespace App\Jobs\Notifications\Webhook;

use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestFailedNotification implements ShouldQueue
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

        foreach ($notificationSettings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'result_id' => $this->result->id,
                    'site_name' => config('app.name'),
                    'service' => Str::title($this->result->service->getLabel()),
                    'serverName' => $this->result->server_name ?? 'Unknown',
                    'serverId' => $this->result->server_id ?? 'Unknown',
                    'errorMessage' => $this->result->data['message'] ?? 'Unknown error during speedtest.',
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
