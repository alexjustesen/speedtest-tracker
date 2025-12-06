<?php

return [
    'title' => 'Datenintegration',
    'label' => 'Datenintegration',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Wenn aktiviert, werden alle neuen Speedtest-Ergebnisse auch an InfluxDB gesendet.',
    'influxdb_v2_enabled' => 'Aktivieren',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://dein-influxdb-Instanz',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Eimer',
    'influxdb_v2_bucket_placeholder' => 'speedtest-Tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'SSL überprüfen',

    // Actions
    'test_connection' => 'Verbindung testen',
    'starting_bulk_data_write_to_influxdb' => 'Starte Massendaten in InfluxDB schreiben',
    'sending_test_data_to_influxdb' => 'Senden von Testdaten an InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Influxdb-Test fehlgeschlagen',
    'influxdb_test_failed_body' => 'Überprüfen Sie die Protokolle für weitere Details.',
    'influxdb_test_success' => 'Testdaten erfolgreich an Influxdb gesendet',
    'influxdb_test_success_body' => 'Testdaten wurden an InfluxDB gesendet. Überprüfen Sie, ob die Daten empfangen wurden.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Fehler beim Erstellen des Schreibens auf Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Überprüfen Sie die Protokolle für weitere Details.',
    'influxdb_bulk_write_success' => 'Massendatenlade für Influxdb abgeschlossen.',
    'influxdb_bulk_write_success_body' => 'Daten wurden an InfluxDB gesendet. Überprüfen Sie, ob die Daten empfangen wurden.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Aktivieren',
    'prometheus_enabled_helper_text' => 'Wenn aktiviert, werden neue Messungen für jeden neuen Geschwindigkeitstest am /prometheus Endpunkt verfügbar sein.',
    'prometheus_allowed_ips' => 'Erlaubte IP-Adressen',
    'prometheus_allowed_ips_helper' => 'Liste der IP-Adressen oder CIDR-Bereiche (z.B. 192.168.1.0/24) denen es erlaubt ist, auf den Mess-Endpunkt zuzugreifen. Leer lassen, um alle IPs zu erlauben.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Eimer',
];
