<?php

namespace App\Console\Commands\Maintenance;

use App\Enums\ResultStatus;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class FixResultStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-result-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reviews the data payload of each result and corrects the status attribute.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();

        $this->info('This will check each result and correct the status to "completed" or "failed" based on the data column.');
        $this->info('📖 Read the docs: https://docs.speedtest-tracker.dev/other/commands');

        if (! $this->confirm('Do you want to continue?')) {
            return Command::FAILURE;
        }

        /**
         * Update completed status
         */
        DB::table('results')
            ->where(function (Builder $query) {
                $query->where('service', '=', 'ookla')
                    ->whereNull('data->level')
                    ->whereNull('data->message');
            })
            ->update([
                'status' => ResultStatus::Completed,
            ]);

        /**
         * Update failed status.
         */
        DB::table('results')
            ->where(function (Builder $query) {
                $query->where('service', '=', 'ookla')
                    ->where('data->level', '=', 'error')
                    ->whereNotNull('data->message');
            })
            ->update([
                'status' => ResultStatus::Failed,
            ]);

        $this->line('✅ finished!');

        return Command::SUCCESS;
    }
}
