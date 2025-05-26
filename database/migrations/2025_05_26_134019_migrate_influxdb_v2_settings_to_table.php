<?php

use App\Settings\DataIntegrationSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateInfluxdbV2SettingsToTable extends Migration
{
    public function up(): void
    {
        // Attempt to load legacy Spatie settings; fall back if unavailable
        try {
            $old = app(DataIntegrationSettings::class);
        } catch (RuntimeException $e) {
            $old = null;
        }

        DB::table('data_integration_settings')->insert([
            'name' => 'InfluxDBv2 Export',
            'type' => 'InfluxDBv2',
            'enabled' => $old->influxdb_v2_enabled ?? false,
            'url' => $old->influxdb_v2_url ?? null,
            'org' => $old->influxdb_v2_org ?? null,
            'bucket' => $old->influxdb_v2_bucket ?? null,
            'token' => $old->influxdb_v2_token ?? null,
            'verify_ssl' => $old->influxdb_v2_verify_ssl ?? true,

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('data_integration_settings')
            ->where('type', 'InfluxDBv2')
            ->delete();
    }
}
