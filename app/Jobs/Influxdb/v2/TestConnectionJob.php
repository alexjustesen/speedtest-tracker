<?php

namespace App\Jobs\Influxdb\v2;

use App\Actions\Influxdb\v2\CreateClient;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use InfluxDB2\ApiException;
use InfluxDB2\Point;

class TestConnectionJob implements ShouldQueue
{
    use Queueable;

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

        $point = Point::measurement('speedtest')
            ->addTag('service', 'faker')
            ->addField('download', (int) 420)
            ->addField('upload', (int) 69)
            ->addField('ping', (float) 4.321)
            ->time(time());

        try {
            $writeApi->write($point);
        } catch (ApiException $e) {
            Log::error('Failed to write test data to Influxdb.', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title(__('jobs.influxdb_test_failed'))
                ->body('Check the logs for more details.')
                ->danger()
                ->sendToDatabase($this->user);

            $writeApi->close();

            return;
        }

        $writeApi->close();

        Notification::make()
            ->title(__('jobs.influxdb_test_success'))
            ->body('Test data has been sent to InfluxDB, check if the data was received.')
            ->success()
            ->sendToDatabase($this->user);
    }
}
