<?php

namespace App\Listeners;

use App\Events\SpeedtestFailed;
use App\Models\Result;
use App\Models\User;
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

        if ($result->dispatched_by && ! $result->scheduled) {
            $this->notifyDispatchingUser($result);
        }
    }

    /**
     * Notify the user who dispatched the speedtest.
     */
    private function notifyDispatchingUser(Result $result): void
    {
        $user = User::find($result->dispatched_by);

        $user->notify(
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
