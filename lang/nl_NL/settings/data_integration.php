<?php

return [
    'title' => 'Data integratie',
    'label' => 'Data integratie',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Wanneer ingeschakeld, zullen alle nieuwe test resultaten ook worden verzonden naar de InfluxDB.',
    'influxdb_v2_enabled' => 'Inschakelen',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://your-influxdb-instance',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Emmer',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Controleer SSL',

    // Actions
    'test_connection' => 'Verbindingstest testen',
    'starting_bulk_data_write_to_influxdb' => 'Alle resultaten naar InfluxDB sturen',
    'sending_test_data_to_influxdb' => 'Testgegevens verzenden naar InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Influxdb test mislukt',
    'influxdb_test_failed_body' => 'Bekijk de logs voor meer details.',
    'influxdb_test_success' => 'Test gegevens succesvol verzonden naar Influxdb',
    'influxdb_test_success_body' => 'Test gegevens zijn verzonden naar de InfluxDB, controleer of de gegevens zijn ontvangen.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Kan schrijven naar Influxdb niet maken.',
    'influxdb_bulk_write_failed_body' => 'Bekijk de logs voor meer details.',
    'influxdb_bulk_write_success' => 'Alle resultaten naar InfluxDB sturen afgerond.',
    'influxdb_bulk_write_success_body' => 'Gegevens zijn verzonden naar InfluxDB, controleer of de gegevens zijn ontvangen.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Inschakelen',
    'prometheus_enabled_helper_text' => 'Wanneer ingeschakeld, zullen statistieken voor elke nieuwe snelheidstest beschikbaar zijn vanaf het /Prometheus eindpunt.',
    'prometheus_allowed_ips' => 'Toegestane IP-adressen',
    'prometheus_allowed_ips_helper' => 'Lijst van IP-adressen of CIDR (bijv. 192.168.1.0/24) toegestaan om het eindpunt van de statistieken te bekijken. Laat leeg om alle IP-adressen toe te staan.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Emmer',
];
