<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
        try {
            Artisan::call('cache:clear');
        } catch (\Throwable $th) {
            Log::info('System maintenance failed to clear the cache.');

            return Command::FAILURE;
        }

        try {
            Artisan::call('view:clear');
        } catch (\Throwable $th) {
            Log::info('System maintenance failed to clear the view cache.');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
