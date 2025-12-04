<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Mail\CompletedSpeedtestMail;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

class ProcessCompletedSpeedtest
{
    public function __construct(
        public NotificationSettings $notificationSettings,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        $result = $event->result;

        $result->loadMissing(['dispatchedBy']);

        // $this->notifyAppriseChannels($result);
        $this->notifyDatabaseChannels($result);
        $this->notifyDispatchingUser($result);
        $this->notifyMailChannels($result);
        $this->notifyWebhookChannels($result);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result): void
    {
        // Don't send Apprise notification if dispatched by a user or test is unhealthy.
        if (filled($result->dispatched_by) || ! $result->healthy) {
            return;
        }

        //
    }

    /**
     * Notify database channels.
     */
    private function notifyDatabaseChannels(Result $result): void
    {
        // Don't send database notification if dispatched by a user or test is unhealthy.
        if (filled($result->dispatched_by) || $result->healthy === false) {
            return;
        }

        // Check if database notifications are enabled.
        if (! $this->notificationSettings->database_enabled || ! $this->notificationSettings->database_on_speedtest_run) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title(__('results.speedtest_completed'))
                ->actions([
                    Action::make('view')
                        ->label(__('general.view'))
                        ->url(route('filament.admin.resources.results.index')),
                ])
                ->success()
                ->sendToDatabase($user);
        }
    }

    /**
     * Notify the user who dispatched the speedtest.
     */
    private function notifyDispatchingUser(Result $result): void
    {
        if (empty($result->dispatched_by) || ! $result->healthy) {
            return;
        }

        $result->dispatchedBy->notify(
            Notification::make()
                ->title(__('results.speedtest_completed'))
                ->actions([
                    Action::make('view')
                        ->label(__('general.view'))
                        ->url(route('filament.admin.resources.results.index')),
                ])
                ->success()
                ->toDatabase(),
        );
    }

    /**
     * Notify mail channels.
     */
    private function notifyMailChannels(Result $result): void
    {
        if (filled($result->dispatched_by) || $result->healthy === false) {
            return;
        }

        if (! $this->notificationSettings->mail_enabled || ! $this->notificationSettings->mail_on_speedtest_run) {
            return;
        }

        if (! count($this->notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($this->notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new CompletedSpeedtestMail($result));
        }
    }

    /**
     * Notify webhook channels.
     */
    private function notifyWebhookChannels(Result $result): void
    {
        // Don't send webhook if dispatched by a user or test is unhealthy.
        if (filled($result->dispatched_by) || $result->healthy === false) {
            return;
        }

        // Check if webhook notifications are enabled.
        if (! $this->notificationSettings->webhook_enabled || ! $this->notificationSettings->webhook_on_speedtest_run) {
            return;
        }

        // Check if webhook urls are configured.
        if (! count($this->notificationSettings->webhook_urls)) {
            Log::warning('Webhook urls not found, check webhook notification channel settings.');

            return;
        }

        foreach ($this->notificationSettings->webhook_urls as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload([
                    'result_id' => $result->id,
                    'site_name' => config('app.name'),
                    'server_name' => Arr::get($result->data, 'server.name'),
                    'server_id' => Arr::get($result->data, 'server.id'),
                    'isp' => Arr::get($result->data, 'isp'),
                    'ping' => $result->ping,
                    'download' => $result->downloadBits,
                    'upload' => $result->uploadBits,
                    'packet_loss' => Arr::get($result->data, 'packetLoss'),
                    'speedtest_url' => Arr::get($result->data, 'result.url'),
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
