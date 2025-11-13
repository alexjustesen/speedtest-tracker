<?php

return [
    'title' => 'Data Integration',
    'label' => 'Data Integration',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'When enabled, all new Speedtest results will also be sent to InfluxDB.',
    'influxdb_v2_enabled' => 'Enable',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://your-influxdb-instance',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Bucket',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Verify SSL',

    // Actions
    'export_current_results' => 'Export current results',
    'test_connection' => 'Test connection',
    'starting_bulk_data_write_to_influxdb' => 'Starting bulk data write to InfluxDB',
    'sending_test_data_to_influxdb' => 'Sending test data to InfluxDB',

    // Common labels (can be removed if they're in general.php)
    'org' => 'Org',
    'bucket' => 'Bucket',
    'token' => 'Token',
];
