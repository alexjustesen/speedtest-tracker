<?php

return [
    'title' => 'Integrace dat',
    'label' => 'Integrace dat',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Pokud je povoleno, všechny nové výsledky testu rychlosti budou zároveň odesílány do InfluxDB.',
    'influxdb_v2_enabled' => 'Povolit',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://tvoje-influxdb-instance',
    'influxdb_v2_org' => 'Organizace',
    'influxdb_v2_bucket' => 'Bucket (úložiště)',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Ověřovat SSL',

    // Actions
    'test_connection' => 'Otestovat připojení',
    'starting_bulk_data_write_to_influxdb' => 'Spouští se hromadný zápis dat do InfluxDB',
    'sending_test_data_to_influxdb' => 'Odesílání testovacích dat do InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Test připojení k InfluxDB selhal',
    'influxdb_test_failed_body' => 'Zkontrolujte logy pro více informací.',
    'influxdb_test_success' => 'Testovací data byla úspěšně odeslána do InfluxDB',
    'influxdb_test_success_body' => 'Testovací data byla odeslána do InfluxDB. Ověřte, zda byla správně přijata.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Hromadný zápis do InfluxDB selhal.',
    'influxdb_bulk_write_failed_body' => 'Zkontrolujte logy pro více informací.',
    'influxdb_bulk_write_success' => 'Hromadné nahrání dat do InfluxDB bylo dokončeno.',
    'influxdb_bulk_write_success_body' => 'Data byla odeslána do InfluxDB. Ověřte, zda byla správně přijata.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Povolit',
    'prometheus_enabled_helper_text' => 'Pokud je povoleno, metriky pro každý nový test rychlosti budou dostupné na endpointu /prometheus.',
    'prometheus_allowed_ips' => 'Povolené IP adresy',
    'prometheus_allowed_ips_helper' => 'Seznam IP adres nebo CIDR rozsahů (např. 192.168.1.0/24), které mají přístup k endpointu s metrikami. Pokud ponecháte prázdné, budou povoleny všechny IP adresy.',

    // Common labels
    'org' => 'Organizace',
    'bucket' => 'Bucket (úložiště)',
];