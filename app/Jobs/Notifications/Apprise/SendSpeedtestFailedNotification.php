<?php

namespace App\Jobs\Notifications\Apprise;

use App\Models\Result;
use App\Settings\NotificationSettings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Handle the event.
     */
    public function handle(): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if (! count($notificationSettings->apprise_webhooks)) {
            Log::warning('Apprise URLs not found, check Apprise notification channel settings.');

            return;
        }

        $payload = view('apprise.speedtest-failed', [
            'id' => $this->result->id,
            'service' => Str::title($this->result->service->getLabel()),
            'serverName' => $this->result->server_name ?? 'Unknown',
            'serverId' => $this->result->server_id ?? 'Unknown',
            'isp' => $this->result->isp ?? 'Unknown',
            'errorMessage' => $this->result->data['message'] ?? 'Unknown error during speedtest.',
            'url' => url('/admin/results'),
        ])->render();

        foreach ($notificationSettings->apprise_webhooks as $webhook) {
            if (empty($webhook['service_url']) || empty($webhook['url'])) {
                Log::warning('Webhook is missing service URL or URL, skipping.');

                continue;
            }

            $webhookPayload = [
                'body' => $payload,
                'title' => "Speedtest Failed - #{$this->result->id}",
                'type' => 'info',
                'urls' => [$webhook['service_url']],
            ];

            try {
                $client = new Client;
                $response = $client->post($webhook['url'], [
                    'json' => $webhookPayload,
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]);

                Log::info('Apprise failed notification sent successfully to '.$webhook['url']);
            } catch (RequestException $e) {
                Log::error('Apprise failed notification failed: '.$e->getMessage());
            }
        }
    }
}
