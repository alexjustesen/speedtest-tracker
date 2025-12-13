<?php

return [
    'title' => 'Integración de datos',
    'label' => 'Integración de datos',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Cuando está activado, todos los nuevos resultados de Speedtest también serán enviados a InfluxDB.',
    'influxdb_v2_enabled' => 'Activar',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://su-instancia-influxdb',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Cubo',
    'influxdb_v2_bucket_placeholder' => 'rastreador de velocidad',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Verificar SSL',

    // Actions
    'test_connection' => 'Probar conexión',
    'starting_bulk_data_write_to_influxdb' => 'Iniciando escritura masiva de datos en InfluxDB',
    'sending_test_data_to_influxdb' => 'Enviando datos de prueba a InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Prueba de Influxdb fallida',
    'influxdb_test_failed_body' => 'Revisa los registros para más detalles.',
    'influxdb_test_success' => 'Datos de prueba enviados con éxito a Influxdb',
    'influxdb_test_success_body' => 'Los datos de prueba han sido enviados a InfluxDB, compruebe si los datos han sido recibidos.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Error al escribir en masa a Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Revisa los registros para más detalles.',
    'influxdb_bulk_write_success' => 'Carga de datos en masa a Influxdb.',
    'influxdb_bulk_write_success_body' => 'Los datos han sido enviados a InfluxDB, compruebe si los datos han sido recibidos.',

    // Prometheus
    'prometheus' => 'Prometeo',
    'prometheus_enabled' => 'Activar',
    'prometheus_enabled_helper_text' => 'Cuando está activado, las métricas para cada prueba de velocidad nueva estarán disponibles en el punto final /prometheus.',
    'prometheus_allowed_ips' => 'Direcciones IP permitidas',
    'prometheus_allowed_ips_helper' => 'Lista de direcciones IP o rangos CIDR (por ejemplo, 192.168.1.0/24) permitieron acceder al extremo de las métricas. Dejar en blanco para permitir todas las IPs.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Cubo',
];
