<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SystemMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Functions to keep Speedtest Tracker nice and healthy.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->refreshCache();

        return Command::SUCCESS;
    }

    /**
     * Clear and refresh the cache.
     */
    protected function refreshCache(): void
    {
        Artisan::call('optimize:clear');

        Artisan::call('optimize');
    }
}
