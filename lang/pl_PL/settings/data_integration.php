<?php

return [
    'title' => 'Integracja danych',
    'label' => 'Integracja danych',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Gdy włączone, wszystkie nowe wyniki testów prędkości zostaną również wysłane do InfluxDB.',
    'influxdb_v2_enabled' => 'Włącz',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://twoja-instancja-influxdb',
    'influxdb_v2_org' => 'Organizacja',
    'influxdb_v2_bucket' => 'Bucket',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Weryfikuj SSL',

    // Actions
    'test_connection' => 'Testuj połączenie',
    'starting_bulk_data_write_to_influxdb' => 'Rozpoczynanie masowego zapisu danych do InfluxDB',
    'sending_test_data_to_influxdb' => 'Wysyłanie danych testowych do InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Test Influxdb nie powiódł się',
    'influxdb_test_failed_body' => 'Sprawdź logi, aby uzyskać więcej szczegółów.',
    'influxdb_test_success' => 'Pomyślnie wysłano dane testowe do Influxdb',
    'influxdb_test_success_body' => 'Dane testowe zostały wysłane do InfluxDB, sprawdź, czy dane zostały otrzymane.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Nie udało się masowo zapisać do Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Sprawdź logi, aby uzyskać więcej szczegółów.',
    'influxdb_bulk_write_success' => 'Zakończono masowe ładowanie danych do Influxdb.',
    'influxdb_bulk_write_success_body' => 'Dane zostały wysłane do InfluxDB, sprawdź, czy dane zostały otrzymane.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Włącz',
    'prometheus_enabled_helper_text' => 'Gdy włączone, metryki dla każdego nowego testu prędkości będą dostępne pod adresem /prometheus.',
    'prometheus_allowed_ips' => 'Dozwolone adresy IP',
    'prometheus_allowed_ips_helper' => 'Lista adresów IP lub zakresów CIDR (np. 192.168.1.0/24) dozwolonych do dostępu do punktu końcowego metryk. Pozostaw puste, aby zezwolić na wszystkie adresy IP.',

    // Common labels
    'org' => 'Organizacja',
    'bucket' => 'Bucket',
];
