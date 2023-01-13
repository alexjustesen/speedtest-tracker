<?php

namespace App\Jobs;

use App\Models\Result;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteResultsData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = Result::count();

        $recipient = User::first();

        try {
            DB::table('results')->truncate();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('There was a problem deleting speedtest results data')
                ->body('Check the logs.')
                ->danger()
                ->sendToDatabase($recipient);

            return 0;
        }

        Notification::make()
            ->title('Speedtest results deleted')
            ->body($count.' speedtest results were deleted from the database.')
            ->success()
            ->sendToDatabase($recipient);
    }
}
