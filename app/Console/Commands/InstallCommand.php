<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A fresh install of Speedtest Tracker.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->option('force')) {
            $this->newLine(2);

            $this->info("Running the install will reset all of the application's data.");
            $this->warn('!!! ALL OF THE DATA WILL BE DELETED !!!');

            if (! $this->confirm('Do you wish to continue?')) {
                $this->info('Install cancelled.');

                return 0;
            }
        }

        $this->info('Starting to install the application...');

        $this->newLine();

        $this->checkAppKey();

        $this->line('⏳ Optimizing the cache...');

        Artisan::call('optimize');

        $this->line('✅ Optimized cache');

        $this->newLine();

        $this->line('⏳ Migrating the database...');

        try {
            Artisan::call('migrate:fresh', [
                '--force' => true,
            ]);
        } catch (\Throwable $th) {
            $this->error('❌ There was an issue migrating the database, check the logs.');

            return 0;
        }

        $this->line('✅ Database migrated');

        $this->newLine();

        $this->line('🚀 Finished installing the application!');

        return 0;
    }

    public function checkAppKey()
    {
        if (empty(config('app.key'))) {
            $this->line('🔑  Creating an application key');

            Artisan::call('key:generate');

            $this->line('✅  Application key created');
        }
    }
}
