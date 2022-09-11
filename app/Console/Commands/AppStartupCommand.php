<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class AppStartupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:startup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs when the app is started to make sure everything is ok';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🐇 Starting up Speedtest Tracker...');
        $this->newLine();

        $this->clearAppCache();

        // $this->checkAppKey();

        // $this->checkAppDatabase();

        // $this->migrateDatabase();

        $this->line('🚀 Speedtest Tracker is ready to roll!');

        return 0;
    }

    private function checkAppDatabase()
    {
        if (! Storage::exists('database.sqlite')) {
            $this->line('🙄 Database not found, creating a new one...');

            $process = new Process(['touch', 'storage/app/database.sqlite']);
            $process->run();

            if (! Storage::exists('database.sqlite')) {
                $this->error('❌ There was an issue creating the database, check the logs');
            }

            $this->line('✅ done');
            $this->newLine();
        }
    }

    private function checkAppKey()
    {
        if (blank(config('app.key'))) {
            $this->line('🔑 Generating a key...');

            Artisan::call('key:generate');

            $this->line('✅ done');
            $this->newLine();
        }
    }

    private function clearAppCache()
    {
        $this->line('💵 Clearing the cache...');

        Artisan::call('optimize');

        $this->line('✅ done');
        $this->newLine();
    }

    private function migrateDatabase()
    {
        $this->line('⏳ Migrating the database...');

        try {
            Artisan::call('migrate', [
                '--database' => 'sqlite',
                '--force' => true,
            ]);
        } catch (\Throwable $th) {
            $this->error('❌ There was an issue migrating the database, check the logs');

            Log::info($th);
        }

        $this->line('✅ done');
        $this->newLine();
    }
}
