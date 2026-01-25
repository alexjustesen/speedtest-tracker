<?php

return [
    /**
     * InfluxDB v2 settings.
     */
    'influxdb_v2_enabled' => env('INFLUXDB_V2_ENABLED', false),

    'influxdb_v2_url' => env('INFLUXDB_V2_URL'),

    'influxdb_v2_org' => env('INFLUXDB_V2_ORG'),

    'influxdb_v2_bucket' => env('INFLUXDB_V2_BUCKET', 'speedtest-tracker'),

    'influxdb_v2_token' => env('INFLUXDB_V2_TOKEN'),

    'influxdb_v2_verify_ssl' => env('INFLUXDB_V2_VERIFY_SSL', true),

    /**
     * Prometheus settings.
     */
    'prometheus_enabled' => env('PROMETHEUS_ENABLED', false),

    'prometheus_allowed_ips' => env('PROMETHEUS_ALLOWED_IPS'),
];
