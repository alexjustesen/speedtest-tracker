<?php

namespace App\Listeners;

use App\Enums\BenchmarkMetric;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Mail\BenchmarkAlarmMail;
use App\Models\Result;
use App\Models\User;
use App\Notifications\Apprise\SpeedtestNotification;
use App\Settings\NotificationSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyBenchmarkFailure
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
    public function handle(SpeedtestBenchmarkUnhealthy $event): void
    {
        $result = $event->result;

        // Don't send notifications for unscheduled speedtests.
        if ($result->unscheduled) {
            return;
        }

        $this->notifyAppriseChannels($result);
        $this->notifyDatabaseChannels();
        $this->notifyMailChannels($result);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result): void
    {
        if (! $this->notificationSettings->apprise_enabled || ! $this->notificationSettings->apprise_on_benchmark_failure) {
            return;
        }

        if (! count($this->notificationSettings->apprise_channel_urls)) {
            Log::warning('Apprise channel URLs not found, check Apprise notification channel settings.');

            return;
        }

        $body = view('apprise.benchmark-alarm', [
            'id' => $result->id,
            'service' => str($result->service->getLabel())->title(),
            'serverName' => $result->server_name,
            'serverId' => $result->server_id,
            'isp' => $result->isp,
            'metrics' => $this->formatBenchmarks($result),
            'speedtest_url' => $result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        $title = 'Speedtest Benchmark Alarm – #'.$result->id;

        foreach ($this->notificationSettings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;

            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            Notification::route('apprise_urls', $channelUrl)
                ->notify(new SpeedtestNotification($title, $body, 'warning', 'markdown'));
        }
    }

    /**
     * Format every configured benchmark on the result for display, so the
     * notification shows the full picture rather than only what changed.
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatBenchmarks(Result $result): array
    {
        return collect($result->benchmarks ?? [])
            ->map(fn (array $benchmark, string $metric): array => [
                'name' => BenchmarkMetric::from($metric)->getLabel(),
                'benchmark' => $benchmark['benchmark_value'].' '.$benchmark['unit'],
                'value' => $benchmark['test_value'].' '.$benchmark['unit'],
                'passed' => $benchmark['passed'],
            ])
            ->values()
            ->all();
    }

    /**
     * Notify database channels.
     */
    private function notifyDatabaseChannels(): void
    {
        if (! $this->notificationSettings->database_enabled || ! $this->notificationSettings->database_on_benchmark_failure) {
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
                ->warning()
                ->sendToDatabase($user);
        }
    }

    /**
     * Notify mail channels.
     */
    private function notifyMailChannels(Result $result): void
    {
        if (! $this->notificationSettings->mail_enabled || ! $this->notificationSettings->mail_on_benchmark_failure) {
            return;
        }

        if (! count($this->notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($this->notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new BenchmarkAlarmMail($result));
        }
    }
}
