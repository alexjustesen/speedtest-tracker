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
    'test_connection' => 'Test connection',
    'starting_bulk_data_write_to_influxdb' => 'Starting bulk data write to InfluxDB',
    'sending_test_data_to_influxdb' => 'Sending test data to InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Influxdb test failed',
    'influxdb_test_failed_body' => 'Check the logs for more details.',
    'influxdb_test_success' => 'Successfully sent test data to Influxdb',
    'influxdb_test_success_body' => 'Test data has been sent to InfluxDB, check if the data was received.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Failed to bulk write to Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Check the logs for more details.',
    'influxdb_bulk_write_success' => 'Finished bulk data load to Influxdb.',
    'influxdb_bulk_write_success_body' => 'Data has been sent to InfluxDB, check if the data was received.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Enable',
    'prometheus_enabled_helper_text' => 'When enabled, metrics for each new speedtest will be available at the /prometheus endpoint.',
    'prometheus_allowed_ips' => 'Allowed IP Addresses',
    'prometheus_allowed_ips_helper' => 'List of IP addresses or CIDR ranges (e.g., 192.168.1.0/24) allowed to access the metrics endpoint. Leave empty to allow all IPs.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Bucket',
];
