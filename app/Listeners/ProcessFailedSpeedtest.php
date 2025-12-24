<?php

namespace App\Listeners;

use App\Events\SpeedtestFailed;
use App\Models\Result;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ProcessFailedSpeedtest
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestFailed $event): void
    {
        $result = $event->result;

        // Notify the user who dispatched the speedtest.
        if ($result->dispatched_by && $result->unscheduled) {
            $result->loadMissing('dispatchedBy');

            $this->notifyDispatchingUser($result);
        }

        // Don't send notifications for unscheduled speedtests.
        if ($result->unscheduled) {
            return;
        }

        // $this->notifyAppriseChannels($result);
    }

    /**
     * Notify Apprise channels.
     */
    private function notifyAppriseChannels(Result $result): void
    {
        //
    }

    /**
     * Notify the user who dispatched the speedtest.
     */
    private function notifyDispatchingUser(Result $result): void
    {
        $result->dispatchedBy->notify(
            Notification::make()
                ->title(__('results.speedtest_failed'))
                ->actions([
                    Action::make('view')
                        ->label(__('general.view'))
                        ->url(route('filament.admin.resources.results.index')),
                ])
                ->warning()
                ->toDatabase(),
        );
    }
}
