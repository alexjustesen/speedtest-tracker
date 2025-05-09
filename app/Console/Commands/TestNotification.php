<?php

namespace App\Console\Commands;

use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification as AppriseCompleted;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification as AppriseThreshold;
use App\Jobs\Notifications\Database\SendSpeedtestCompletedNotification as DatabaseCompleted;
use App\Jobs\Notifications\Database\SendSpeedtestThresholdNotification as DatabaseThreshold;
use App\Jobs\Notifications\Mail\SendSpeedtestCompletedNotification as MailCompleted;
use App\Jobs\Notifications\Mail\SendSpeedtestThresholdNotification as MailThreshold;
use App\Jobs\Notifications\Webhook\SendSpeedtestCompletedNotification as WebhookCompleted;
use App\Jobs\Notifications\Webhook\SendSpeedtestThresholdNotification as WebhookThreshold;
use App\Models\Result;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    protected $signature = 'notification:test 
                            {type=completed : Notification type (completed or threshold)} 
                            {--channel=apprise : Notification channel (apprise, mail, database, webhook)}';

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
            'mail-completed' => MailCompleted::dispatch($result),
            'mail-threshold' => MailThreshold::dispatch($result),
            'database-completed' => DatabaseCompleted::dispatch($result),
            'database-threshold' => DatabaseThreshold::dispatch($result),
            'webhook-completed' => WebhookCompleted::dispatch($result),
            'webhook-threshold' => WebhookThreshold::dispatch($result),
        };

        $this->info('✅ Notification dispatched!');

        return self::SUCCESS;
    }
}
