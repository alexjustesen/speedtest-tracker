<?php

namespace App\Listeners;

use App\Events\SpeedtestBenchmarkFailed;
use App\Helpers\Number;
use App\Mail\UnhealthySpeedtestMail;
use App\Models\Result;
use App\Models\User;
use App\Notifications\Apprise\SpeedtestNotification;
use App\Settings\NotificationSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\WebhookServer\WebhookCall;

class ProcessUnhealthySpeedtest
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

        $this->notifyAppriseChannels($result);
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
        // Don't send Apprise notification if dispatched by a user.
        if (filled($result->dispatched_by)) {
            return;
        }

        if (! $this->notificationSettings->apprise_enabled || ! $this->notificationSettings->apprise_on_threshold_failure) {
            return;
        }

        if (! count($this->notificationSettings->apprise_channel_urls)) {
            Log::warning('Apprise channel URLs not found, check Apprise notification channel settings.');

            return;
        }

        if (empty($result->benchmarks)) {
            Log::warning('Benchmark data not found, won\'t send Apprise notification.');

            return;
        }

        // Build metrics array from failed benchmarks
        $failed = [];

        foreach ($result->benchmarks as $metric => $benchmark) {
            if ($benchmark['passed'] === false) {
                $failed[] = [
                    'name' => ucfirst($metric),
                    'threshold' => $benchmark['value'].' '.$benchmark['unit'],
                    'value' => $this->formatMetricValue($metric, $result),
                ];
            }
        }

        if (! count($failed)) {
            Log::warning('No failed thresholds found in benchmarks, won\'t send Apprise notification.');

            return;
        }

        $body = view('apprise.speedtest-threshold', [
            'id' => $result->id,
            'service' => Str::title($result->service->getLabel()),
            'serverName' => $result->server_name,
            'serverId' => $result->server_id,
            'isp' => $result->isp,
            'metrics' => $failed,
            'speedtest_url' => $result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        $title = 'Speedtest Threshold Breach – #'.$result->id;

        // Send notification to each configured channel URL
        foreach ($this->notificationSettings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;
            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            Notification::route('apprise_urls', $channelUrl)
                ->notify(new SpeedtestNotification($title, $body, 'warning'));
        }
    }

    /**
     * Format metric value for display in notification.
     */
    private function formatMetricValue(string $metric, Result $result): string
    {
        return match ($metric) {
            'download' => Number::toBitRate(bits: $result->download_bits, precision: 2),
            'upload' => Number::toBitRate(bits: $result->upload_bits, precision: 2),
            'ping' => round($result->ping, 2).' ms',
            default => '',
        };
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
            FilamentNotification::make()
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
            FilamentNotification::make()
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
        // Don't send mail if dispatched by a user.
        if (filled($result->dispatched_by)) {
            return;
        }

        // Check if mail notifications are enabled.
        if (! $this->notificationSettings->mail_enabled || ! $this->notificationSettings->mail_on_threshold_failure) {
            return;
        }

        // Check if mail recipients are configured.
        if (! count($this->notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($this->notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new UnhealthySpeedtestMail($result));
        }
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
