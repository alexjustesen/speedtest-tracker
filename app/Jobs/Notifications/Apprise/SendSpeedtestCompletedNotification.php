<?php

namespace App\Jobs\Notifications\Apprise;

use App\Enums\UserRole;
use App\Models\Result;
use App\Models\User;
use App\Services\Notifications\SpeedtestNotificationData;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Handle the event.
     */
    public function handle(): void
    {
        $notificationSettings = app(NotificationSettings::class);

        if (! count($notificationSettings->apprise_webhooks)) {
            Log::warning('Apprise URLs not found, check Apprise notification channel settings.');

            return;
        }

        $data = SpeedtestNotificationData::make($this->result);

        $payload = view('apprise.speedtest-completed', $data)->render();

        foreach ($notificationSettings->apprise_webhooks as $webhook) {
            if (empty($webhook['service_url']) || empty($webhook['url'])) {
                Log::warning('Webhook is missing service URL or URL, skipping.');

                continue;
            }

            $webhookPayload = [
                'body' => $payload,
                'title' => 'Speedtest Completed - #'.$this->result->id,
                'type' => 'info',
                'urls' => $webhook['service_url'],
            ];

            try {
                Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($webhook['url'], $webhookPayload)->throw();

                Log::info('Apprise notification sent successfully to instance '.$webhook['url'].' and service url '.$webhook['service_url']);
            } catch (\Throwable $e) {
                Log::error('Apprise notification failed for instance '.$webhook['url'].' and service URL '.$webhook['service_url'].': '.$e->getMessage());

                // Notify admins if notifications fail.
                $admins = User::where('role', UserRole::Admin)->get();
                Notification::make()
                    ->title('Apprise Notification Failure')
                    ->danger()
                    ->body('Failed to send notification. Please check the logs.')
                    ->sendToDatabase($admins);
            }
        }
    }
}
