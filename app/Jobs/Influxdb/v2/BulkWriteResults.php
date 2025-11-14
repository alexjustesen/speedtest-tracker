<?php

namespace App\Jobs\Influxdb\v2;

use App\Actions\Influxdb\v2\BuildPointData;
use App\Actions\Influxdb\v2\CreateClient;
use App\Enums\ResultStatus;
use App\Models\Result;
use App\Models\User;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BulkWriteResults implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = CreateClient::run();

        $writeApi = $client->createWriteApi();

        Result::query()
            ->where('status', '=', ResultStatus::Completed)
            ->chunkById(100, function (Collection $results) use ($writeApi) {
                $points = [];

                foreach ($results as $result) {
                    $points[] = BuildPointData::run($result);
                }

                try {
                    $writeApi->write($points);
                } catch (Exception $e) {
                    Log::error('Failed to bulk write to InfluxDB.', [
                        'error' => $e->getMessage(),
                    ]);

                    Notification::make()
                        ->title(__('settings/data_integration.influxdb_bulk_write_failed'))
                        ->body(__('settings/data_integration.influxdb_bulk_write_failed_body'))
                        ->danger()
                        ->sendToDatabase($this->user);

                    $this->fail($e);

                    $writeApi->close();

                    return;
                }
            });

        $writeApi->close();

        Notification::make()
            ->title(__('settings/data_integration.influxdb_bulk_write_success'))
            ->body(__('settings/data_integration.influxdb_bulk_write_success_body'))
            ->success()
            ->sendToDatabase($this->user);
    }
}
