<?php

return [
    'title' => 'Resultados',
    'result_overview' => 'Visão geral do resultado',
    'error_message_title' => 'Mensagem de erro',

    // Metrics
    'download' => 'Download',
    'download_latency_high' => 'Latência de Download alta',
    'download_latency_low' => 'Latência de Download baixa',
    'download_latency_iqm' => 'Latência de Download IQM',
    'download_latency_jitter' => 'Jitter de latência de Download',

    'upload' => 'Upload',
    'upload_latency_high' => 'Latência de Upload alta',
    'upload_latency_low' => 'Latência de Upload baixa',
    'upload_latency_iqm' => 'Latência de Upload IQM',
    'upload_latency_jitter' => 'Jitter de latência de Upload',

    'ping' => 'Latência',
    'ping_details' => 'Detalhes do Ping',
    'ping_jitter' => 'Jitter do Ping',
    'ping_high' => 'Latência de ping alta',
    'ping_low' => 'Latência de ping baixa',

    'packet_loss' => 'Perda de pacote',
    'iqm' => 'IQM',

    // Server & metadata
    'server_&_metadata' => 'Servidor e Metadados',
    'server_id' => 'ID do Servidor',
    'server_host' => 'Host do servidor',
    'server_name' => 'Nome do servidor',
    'server_location' => 'Localização do servidor',
    'service' => 'Serviço',
    'isp' => 'Provedor',
    'ip_address' => 'Endereço IP',
    'scheduled' => 'Agendado',

    // Filters
    'only_healthy_speedtests' => 'Apenas testes de velocidade saudáveis',
    'only_unhealthy_speedtests' => 'Apenas testes de velocidade não saudáveis',
    'only_manual_speedtests' => 'Apenas testes de velocidade manuais',
    'only_scheduled_speedtests' => 'Apenas testes de velocidade agendados',
    'created_from' => 'Criado por',
    'created_until' => 'Criado até',

    // Export
    'export_all_results' => 'Exportar todos resultados',
    'export_all_results_description' => 'Irá exportar todas as colunas para todos os resultados.',
    'export_completed' => 'Exportação concluída, :count :rows exportados.',
    'failed_export' => ':count :rows falhou ao exportar.',
    'row' => '{1} :count fileira [2,*] :count linhas',

    // Actions
    'update_comments' => 'Atualizar comentários',
    'truncate_results' => 'Truncar resultados',
    'truncate_results_description' => 'Tem certeza que deseja truncar todos os resultados? Esta ação é irreversível.',
    'truncate_results_success' => 'Tabela de resultados truncada!',
    'view_on_speedtest_net' => 'Ver em Speedtest.net',

    // Notifications
    'speedtest_benchmark_passed' => 'Referência do teste de velocidade aprovada',
    'speedtest_benchmark_failed' => 'Referência do teste de velocidade falhou',
    'speedtest_started' => 'Teste de velocidade iniciado',
    'speedtest_completed' => 'Teste de velocidade concluído',
    'speedtest_failed' => 'Teste de velocidade falhou',
    'download_threshold_breached' => 'Limite de Download violado!',
    'upload_threshold_breached' => 'Limite de Upload violado!',
    'ping_threshold_breached' => 'Limite de ping violado!',

    // Run Speedtest Action
    'speedtest' => 'Teste de velocidade',
    'public_dashboard' => 'Painel público',
    'select_server' => 'Selecionar servidor',
    'select_server_helper' => 'Deixe em branco para executar o acelerador sem especificar um servidor. Os servidores bloqueados serão ignorados.',
    'manual_servers' => 'Servidores manuais',
    'closest_servers' => 'Servidores mais próximos',
    'run_speedtest' => 'Executar teste de velocidade',
    'start' => 'Iniciar',
];
