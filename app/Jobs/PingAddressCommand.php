<?php

namespace App\Jobs;

use App\Models\PingAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PingAddressCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
            public PingAddress $pingAddress
        ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // https://github.com/striebwj/laravel-ping/blob/master/src/LaravelPing.php

        // create a new cURL resource
        $ch = curl_init($this->pingAddress->url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Speedtest Tracker (https://docs.speedtest-tracker.dev)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // grab URL and pass it to the browser
        curl_exec($ch);

        $data = curl_getinfo($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);
    }
}
