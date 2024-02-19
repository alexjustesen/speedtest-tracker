<?php

namespace App\Actions;

use App\Enums\ResultStatus;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsAction;

class MigrateBadJsonResults
{
    use AsAction;

    public int $jobTimeout = 60 * 5;

    public int $jobTries = 1;

    public function handle(User $user)
    {
        $tableName = 'results_bad_json';

        if (! Schema::hasTable('results')) {
            Notification::make()
                ->title('❌ Could not migrate bad json results!')
                ->body('The "results" table is missing.')
                ->danger()
                ->sendToDatabase($user);

            return;
        }

        if (! Schema::hasTable($tableName)) {
            Notification::make()
                ->title('❌ Could not migrate bad json results!')
                ->body('The "results_bad_json" table is missing.')
                ->danger()
                ->sendToDatabase($user);

            return;
        }

        /**
         * Copy backup data to the new results table and reformat it.
         */
        try {
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
        } catch (\Throwable $e) {
            Log::error($e);

            Notification::make()
                ->title('There was an issue migrating the data!')
                ->body('Check the logs for an output of the issue.')
                ->danger()
                ->sendToDatabase($user);

            return;
        }

        Notification::make()
            ->title('Data migration completed!')
            ->body('Your history has been successfully migrated.')
            ->success()
            ->sendToDatabase($user);
    }
}
