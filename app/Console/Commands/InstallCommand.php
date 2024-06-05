<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

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
    protected $description = 'Install a fresh version of the Speedtest Tracker application.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $this->option('force')) {
            $confirmed = confirm('Are you sure you want to continue?');

            if (! $confirmed) {
                $this->fail('Application install cancelled.');
            }
        }

        $this->info('⏳ Starting to install the application...');

        if (app()->environment('production') || app()->environment('testing')) {
            $this->call('filament:cache-components');
            $this->call('optimize');
        } else {
            $this->call('optimize:clear');
        }

        try {
            $this->call('migrate:fresh', [
                '--force' => true,
            ]);
        } catch (\Throwable $th) {
            $this->fail('❌ There was an issue migrating the database, check the logs.');
        }

        info('🚀 Finished installing Speedtest Tracker!');
    }
}
