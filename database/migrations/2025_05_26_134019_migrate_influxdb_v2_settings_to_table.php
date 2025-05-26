<?php

use App\Settings\DataIntegrationSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateInfluxdbV2SettingsToTable extends Migration
{
    public function up(): void
    {
        // Load legacy settings if available
        try {
            $old = app(DataIntegrationSettings::class);
        } catch (\RuntimeException $e) {
            $old = null;
        }

        // Build the JSON payload
        $payload = [
            'url' => $old->influxdb_v2_url ?? null,
            'org' => $old->influxdb_v2_org ?? null,
            'bucket' => $old->influxdb_v2_bucket ?? null,
            'token' => $old->influxdb_v2_token ?? null,
            'verify_ssl' => $old->influxdb_v2_verify_ssl ?? true,
        ];

        DB::table('data_integration_settings')->insert([
            'type' => 'InfluxDBv2',
            'name' => 'Default InfluxDBv2 Exporter',
            'enabled' => $old->influxdb_v2_enabled ?? false,
            'config' => json_encode($payload),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('data_integration_settings')
            ->where('type', 'influxdb_v2')
            ->delete();
    }
}
