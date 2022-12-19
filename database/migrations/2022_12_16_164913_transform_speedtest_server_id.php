<?php

use App\Settings\GeneralSettings;
use Illuminate\Database\Migrations\Migration;

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

        $settings->speedtest_server = [$settings->speedtest_server];
        $settings->save();
    }
};
