<?php

namespace App\Console\Commands;

use App\Enums\ResultStatus;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ResultFixStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:result-fix-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reviews the data payload of each result and corrects the status attribute.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->newLine();

        $this->info(__('translations.status_fix.info_1'));
        $this->info(__('translations.status_fix.info_2'));

        if (! $this->confirm(__('translations.confirm'))) {
            $this->fail(__('translations.fail'));
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

        $this->line(__('translations.status_fix.finished'));
    }
}
