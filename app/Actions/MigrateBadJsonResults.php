<?php

namespace App\Actions;

use App\Enums\ResultStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

class MigrateBadJsonResults
{
    use AsAction;

    public int $jobTimeout = 60 * 5;

    public int $jobTries = 1;

    public function handle()
    {
        $tableName = 'results_bak_bad_json';

        if (! Schema::hasTable('results')) {
            Log::info('❌ Could not migrate bad json results, "results" table is missing.');

            return;
        }

        if (! Schema::hasTable('results_bak_bad_json')) {
            Log::info('❌ Could not migrate bad json results, "results_bak_bad_json" table is missing.');

            return;
        }

        /**
         * Copy backup data to the new results table and reformat it.
         */
        DB::table($tableName)->chunkById(100, function ($results) {
            foreach ($results as $result) {
                $record = [
                    'service' => 'ookla',
                    'ping' => $result->ping,
                    'download' => $result->download,
                    'upload' => $result->upload,
                    'comments' => $result->comments,
                    'data' => json_decode($result->data),
                    'status' => match ($result->successful) {
                        1 => ResultStatus::Completed,
                        default => ResultStatus::Failed,
                    },
                    'scheduled' => $result->scheduled,
                    'created_at' => $result->created_at,
                    'updated_at' => now(),
                ];

                DB::table('results')->insert($record);
            }
        });
    }
}
