<?php

namespace App\Console\Commands;

use App\Settings\QuotaSettings;
use Illuminate\Console\Command;

class ResetQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-quota
                            {--force : Force the reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the used quota to zero.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('Do you want to continue?')) {
                $this->fail('Command cancelled.');
            }
        }

        $settings = new QuotaSettings();
        $settings->used = 0;
        $settings->save();

        $this->info('Quota has been reset successfully.');
    }
}
