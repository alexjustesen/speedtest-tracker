<?php

namespace App\Listeners;

use App\Events\SpeedtestBenchmarkFailed;
use App\Models\Result;
use App\Models\User;
use App\Settings\NotificationSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class ProcessSpeedtestBenchmarkFailed
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public NotificationSettings $notificationSettings,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SpeedtestBenchmarkFailed $event): void
    {
        $result = $event->result;

        $result->loadMissing(['dispatchedBy']);

        // $this->notifyAppriseChannels($result);
        $this->notifyDatabaseChannels($result);
        $this->notifyDispatchingUser($result);
        // $this->notifyMailChannels($result);
        $this->notifyWebhookChannels($result);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result): void
    {
        //
    }

    /**
     * Notify database channels.
     */
    private function notifyDatabaseChannels(Result $result): void
    {
        // Don't send database notification if dispatched by a user.
        if (filled($result->dispatched_by)) {
            return;
        }

        // Check if database notifications are enabled.
        if (! $this->notificationSettings->database_enabled || ! $this->notificationSettings->database_on_threshold_failure) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title(__('results.speedtest_benchmark_failed'))
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
        if (empty($result->dispatched_by)) {
            return;
        }

        $result->dispatchedBy->notify(
            Notification::make()
                ->title(__('results.speedtest_benchmark_failed'))
                ->actions([
                    Action::make('view')
                        ->label(__('general.view'))
                        ->url(route('filament.admin.resources.results.index')),
                ])
                ->warning()
                ->toDatabase(),
        );
    }

    /**
     * Notify mail channels.
     */
    private function notifyMailChannels(Result $result): void
    {
        //
    }

    /**
     * Notify webhook channels.
     */
    private function notifyWebhookChannels(Result $result): void
    {
        // Don't send webhook if dispatched by a user.
        if (filled($result->dispatched_by)) {
            return;
        }

        // Check if webhook notifications are enabled.
        if (! $this->notificationSettings->webhook_enabled || ! $this->notificationSettings->webhook_on_threshold_failure) {
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
                    'isp' => $result->isp,
                    'benchmarks' => $result->benchmarks,
                    'speedtest_url' => $result->result_url,
                    'url' => url('/admin/results'),
                ])
                ->doNotSign()
                ->dispatch();
        }
    }
}
