<?php

namespace App\Listeners;

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Events\BenchmarkAlarmsRecovered;
use App\Events\BenchmarkAlarmsTriggered;
use App\Mail\BenchmarkAlarmMail;
use App\Models\Result;
use App\Models\User;
use App\Notifications\Apprise\SpeedtestNotification;
use App\Settings\NotificationSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyBenchmarkAlarmState
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public NotificationSettings $notificationSettings,
    ) {}

    /**
     * Handle benchmarks entering an alarm state.
     */
    public function handleTriggered(BenchmarkAlarmsTriggered $event): void
    {
        $this->notify($event->result, BenchmarkState::Alarm);
    }

    /**
     * Handle benchmarks recovering from an alarm state.
     */
    public function handleRecovered(BenchmarkAlarmsRecovered $event): void
    {
        $this->notify($event->result, BenchmarkState::Ok);
    }

    /**
     * Notify all enabled channels for the given benchmark state change.
     */
    private function notify(Result $result, BenchmarkState $state): void
    {
        // Don't send notifications for unscheduled speedtests.
        if ($result->unscheduled) {
            return;
        }

        $recovered = $state === BenchmarkState::Ok;

        $this->notifyAppriseChannels($result, $recovered);
        $this->notifyDatabaseChannels($recovered);
        $this->notifyMailChannels($result, $state);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result, bool $recovered): void
    {
        $enabled = $recovered
            ? $this->notificationSettings->apprise_on_benchmark_recovery
            : $this->notificationSettings->apprise_on_benchmark_failure;

        if (! $this->notificationSettings->apprise_enabled || ! $enabled) {
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
            'recovered' => $recovered,
        ])->render();

        $title = $recovered
            ? 'Speedtest Benchmark Recovered – #'.$result->id
            : 'Speedtest Benchmark Alarm – #'.$result->id;

        foreach ($this->notificationSettings->apprise_channel_urls as $row) {
            $channelUrl = $row['channel_url'] ?? null;

            if (! $channelUrl) {
                Log::warning('Skipping entry with missing channel_url.');

                continue;
            }

            Notification::route('apprise_urls', $channelUrl)
                ->notify(new SpeedtestNotification($title, $body, $recovered ? 'success' : 'warning', 'markdown'));
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
    private function notifyDatabaseChannels(bool $recovered): void
    {
        $enabled = $recovered
            ? $this->notificationSettings->database_on_benchmark_recovery
            : $this->notificationSettings->database_on_benchmark_failure;

        if (! $this->notificationSettings->database_enabled || ! $enabled) {
            return;
        }

        foreach (User::all() as $user) {
            $notification = FilamentNotification::make()
                ->title($recovered ? __('results.speedtest_benchmark_recovered') : __('results.speedtest_benchmark_failed'))
                ->actions([
                    Action::make('view')
                        ->label(__('general.view'))
                        ->url(route('filament.admin.resources.results.index')),
                ]);

            $recovered ? $notification->success() : $notification->warning();

            $notification->sendToDatabase($user);
        }
    }

    /**
     * Notify mail channels.
     */
    private function notifyMailChannels(Result $result, BenchmarkState $state): void
    {
        $recovered = $state === BenchmarkState::Ok;

        $enabled = $recovered
            ? $this->notificationSettings->mail_on_benchmark_recovery
            : $this->notificationSettings->mail_on_benchmark_failure;

        if (! $this->notificationSettings->mail_enabled || ! $enabled) {
            return;
        }

        if (! count($this->notificationSettings->mail_recipients)) {
            Log::warning('Mail recipients not found, check mail notification channel settings.');

            return;
        }

        foreach ($this->notificationSettings->mail_recipients as $recipient) {
            Mail::to($recipient)
                ->send(new BenchmarkAlarmMail($result, $state));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            BenchmarkAlarmsTriggered::class => 'handleTriggered',
            BenchmarkAlarmsRecovered::class => 'handleRecovered',
        ];
    }
}
