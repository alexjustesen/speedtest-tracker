<?php

namespace App\Jobs\Notifications\Database;

use App\Models\Result;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSpeedtestFailedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Result $result,
    ) {}

    /**
     * Handle the job.
     */
    public function handle(): void
    {
        $errorMessage = $this->result->data['message'] ?? 'Unknown error during speedtest.';

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Speedtest failed')
                ->body("Failure reason: {$errorMessage}")
                ->danger()
                ->sendToDatabase($user);
        }
    }
}
