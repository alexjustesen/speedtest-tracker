<?php

return [
    'title' => 'Integração de dados',
    'label' => 'Integração de dados',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Quando ativado, todos os novos resultados de Speedtest também serão enviados para InfluxDB.',
    'influxdb_v2_enabled' => 'Habilitado',
    'influxdb_v2_url' => 'URL:',
    'influxdb_v2_url_placeholder' => 'http://sua-influxdb-instância',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Bucket',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Verificar SSL',

    // Actions
    'test_connection' => 'Testar conexão',
    'starting_bulk_data_write_to_influxdb' => 'Iniciando dados em massa no InfluxDB',
    'sending_test_data_to_influxdb' => 'Enviando dados de teste para InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Falha no teste Influxdb',
    'influxdb_test_failed_body' => 'Confira os logs para mais detalhes.',
    'influxdb_test_success' => 'Dados de teste enviados com sucesso para o Influxdb',
    'influxdb_test_success_body' => 'Dados de teste enviados para InfluxDB, verifique se os dados foram recebidos.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Falha ao escrever no Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Confira os logs para mais detalhes.',
    'influxdb_bulk_write_success' => 'Carga massiva de dados concluída para o Influxdb.',
    'influxdb_bulk_write_success_body' => 'Os dados foram enviados para InfluxDB, verifique se os dados foram recebidos.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Habilitado',
    'prometheus_enabled_helper_text' => 'Quando ativado, as métricas para cada novo radar estarão disponíveis no ponto de extremidade do /prometheus.',
    'prometheus_allowed_ips' => 'Endereços de IP Permitidos',
    'prometheus_allowed_ips_helper' => 'Lista de endereços IP ou intervalos de CIDR (por exemplo, 192.168.1.0/24) permitidos de acessar o ponto de extremidade das métricas. Deixe em branco para permitir que todos os IPs.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Bucket',
];
