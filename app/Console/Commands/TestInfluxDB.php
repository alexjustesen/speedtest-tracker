<?php

namespace App\Console\Commands;

use App\Models\Result;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class TestInfluxDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-influxdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write a test log to InfluxDB to make sure the config works.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (File::exists(base_path().'/config.yml')) {
            $config = Yaml::parseFile(
                base_path().'/config.yml'
            );
        }

        if (File::exists('/app/config.yml')) {
            $config = Yaml::parseFile('/app/config.yml');
        }

        $influxdb = $config['influxdb'];

        if ($influxdb['enabled'] == true) {
            $result = Result::factory()->create();
        }

        return 0;
    }
}
