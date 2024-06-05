<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\SystemChecker;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class VersionChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a notification to the admin users when Speedtest Tracker is outdated.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $system = new SystemChecker;

        if (! $system->isOutOfDate()) {
            return;
        }

        $admins = User::where('role', '=', UserRole::Admin)->get();

        foreach ($admins as $user) {
            Notification::make()
                ->title('Update Available')
                ->body("There's a new version of Speedtest Tracker available: {$system->getRemoteVersion()}")
                ->info()
                ->actions([
                    Action::make('view releases')
                        ->button()
                        ->color('gray')
                        ->url('https://github.com/alexjustesen/speedtest-tracker/releases')
                        ->openUrlInNewTab(),
                ])
                ->sendToDatabase($user);
        }
    }
}
