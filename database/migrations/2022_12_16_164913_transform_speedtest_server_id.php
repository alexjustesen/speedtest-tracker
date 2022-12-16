<?php

use App\Settings\GeneralSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = new GeneralSettings();

        if ($settings->speedtest_server == 'null') {
            Log::info('Skipping transform speedtest server id, id is empty.');

            return 0;
        }

        $settings->speedtest_server = [$settings->speedtest_server];
        $settings->save();

        Log::info('Transformed speedtest server id to array format.');
    }
};
