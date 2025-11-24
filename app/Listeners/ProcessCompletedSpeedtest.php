<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Models\Result;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ProcessCompletedSpeedtest
{
    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
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
}
