<?php

use App\Settings\GeneralSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $settings = new GeneralSettings();

            $settings->speedtest_server = [$settings->speedtest_server];
            $settings->save();
        } catch (Throwable $th) {
            // This code is short lived as it'll be replaced with a jobs table...
        }
    }
};
