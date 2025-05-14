<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\table;

class OoklaListServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ookla-list-servers
                            {search? : Search for a server by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a list of local Ookla speedtest servers.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = Http::retry(3, 250)->get(
            url: 'https://www.speedtest.net/api/js/servers',
            query: [
                'engine' => 'js',
                'https_functional' => true,
                'search' => $this->argument('search'),
                'limit' => 20, // 20 is the max returned by the api
            ],
        );

        if ($response->failed()) {
            $this->fail('There was an issue retrieving a list of speedtest servers, check the logs.');
        }

        $fields = ['id', 'sponsor', 'name', 'country', 'distance'];

        table(
            headers: $fields,
            rows: $response->collect()->select($fields),
        );
    }
}
