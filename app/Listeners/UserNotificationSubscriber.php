<?php

namespace App\Listeners;

use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Events\Dispatcher;

class UserNotificationSubscriber
{
    /**
     * Handle the event.
     */
    public function handleCompleted(SpeedtestCompleted $event): void
    {
        $result = $event->result;

        if (empty($result->dispatched_by)) {
            return;
        }

        $result->loadMissing('dispatchedBy');

        Notification::make()
            ->title(__('results.speedtest_completed'))
            ->actions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->url(route('filament.admin.resources.results.index')),
            ])
            ->success()
            ->sendToDatabase($result->dispatchedBy);
    }

    /**
     * Handle the event.
     */
    public function handleBenchmarkFailed(SpeedtestBenchmarkUnhealthy $event): void
    {
        $result = $event->result;

        if (empty($result->dispatched_by)) {
            return;
        }

        // Don't send notifications for unscheduled speedtests.
        if ($result->unscheduled) {
            return;
        }

        $result->loadMissing('dispatchedBy');

        Notification::make()
            ->title(__('results.speedtest_benchmark_failed'))
            ->actions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->url(route('filament.admin.resources.results.index')),
            ])
            ->warning()
            ->sendToDatabase($result->dispatchedBy);
    }

    /**
     * Handle the event.
     */
    public function handleFailed(SpeedtestFailed $event): void
    {
        $result = $event->result;

        if (empty($result->dispatched_by)) {
            return;
        }

        $result->loadMissing('dispatchedBy');

        Notification::make()
            ->title(__('results.speedtest_failed'))
            ->actions([
                Action::make('view')
                    ->label(__('general.view'))
                    ->url(route('filament.admin.resources.results.index')),
            ])
            ->warning()
            ->sendToDatabase($result->dispatchedBy);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            SpeedtestCompleted::class => 'handleCompleted',
            SpeedtestBenchmarkUnhealthy::class => 'handleBenchmarkFailed',
            SpeedtestFailed::class => 'handleFailed',
        ];
    }
}
