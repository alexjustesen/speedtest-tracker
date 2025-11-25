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

        $result->loadMissing(['dispatchedBy']);

        $this->notifyDispatchingUser($result);
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
