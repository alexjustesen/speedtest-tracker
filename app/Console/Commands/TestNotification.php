<?php

namespace App\Console\Commands;

use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification as AppriseCompleted;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification as AppriseThreshold;
use App\Models\Result;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    protected $signature = 'notification:test 
                            {type=completed : Notification type (completed or threshold)} 
                            {--channel=apprise : Notification channel (apprise)}';

    protected $description = 'Send a test notification using a fake result';

    public function handle(): int
    {
        $type = $this->argument('type');
        $channel = $this->option('channel');

        $this->info("Creating fake result for type: {$type}");

        $result = Result::factory()->create([
            'status' => 'completed',
        ]);

        $this->info("Dispatching {$channel} notification...");

        match ("{$channel}-{$type}") {
            'apprise-completed' => AppriseCompleted::dispatch($result),
            'apprise-threshold' => AppriseThreshold::dispatch($result),
        };

        $this->info('✅ Notification dispatched!');

        return self::SUCCESS;
    }
}
