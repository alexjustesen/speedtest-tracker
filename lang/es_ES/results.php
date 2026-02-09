<?php

return [
    'title' => 'Resultados',
    'result_overview' => 'Resumen de resultados',
    'error_message_title' => 'Mensaje de error',

    // Metrics
    'download' => 'Descargar',
    'download_latency_high' => 'Latencia de descarga alta',
    'download_latency_low' => 'Latencia de descarga baja',
    'download_latency_iqm' => 'Latencia de descarga IQM',
    'download_latency_jitter' => 'Variación de latencia de descarga',

    'upload' => 'Subida',
    'upload_latency_high' => 'Latencia de subida alta',
    'upload_latency_low' => 'Latencia de subida baja',
    'upload_latency_iqm' => 'Latencia de subida IQM',
    'upload_latency_jitter' => 'Variación de latencia de subida',

    'ping' => 'Ping',
    'ping_details' => 'Detalles de ping',
    'ping_jitter' => 'Variación de ping',
    'ping_high' => 'Ping alto',
    'ping_low' => 'Ping bajo',

    'packet_loss' => 'Paquetes perdidos',
    'iqm' => 'IQM',

    // Server & metadata
    'server_&_metadata' => 'Servidor y metadatos',
    'server_id' => 'ID del servidor',
    'server_host' => 'Host del servidor',
    'server_name' => 'Nombre del servidor',
    'server_location' => 'Ubicación del servidor',
    'service' => 'Servicio',
    'isp' => 'Proveedor de internet',
    'ip_address' => 'Dirección IP',
    'scheduled' => 'Programado',

    // Filters
    'only_healthy_speedtests' => 'Solo pruebas de velocidad saludables',
    'only_unhealthy_speedtests' => 'Sólo pruebas de velocidad no saludables',
    'only_manual_speedtests' => 'Solo pruebas de velocidad manual',
    'only_scheduled_speedtests' => 'Solo pruebas de velocidad programadas',
    'created_from' => 'Creado desde',
    'created_until' => 'Creado hasta',

    // Export
    'export_all_results' => 'Exportar todos los resultados',
    'export_all_results_description' => 'Exportará todas las columnas para todos los resultados.',
    'export_completed' => 'Exportación completada, :count :rows exportadas.',
    'failed_export' => ':count  :ros exportación fallida.',
    'row' => '{1} :count fila|[2,*] :count filas',

    // Actions
    'update_comments' => 'Actualizar comentarios',
    'view_on_speedtest_net' => 'Ver en Speedtest.net',

    // Notifications
    'speedtest_benchmark_passed' => 'Prueba de velocidad superada',
    'speedtest_benchmark_failed' => 'Prueba de rendimiento de velocidad fallida',
    'speedtest_started' => 'Prueba de velocidad iniciada',
    'speedtest_completed' => 'Prueba de velocidad completada',
    'speedtest_failed' => 'Prueba de velocidad fallida',
    'download_threshold_breached' => '¡Umbral de descarga superado!',
    'upload_threshold_breached' => '¡Umbral de subida superado!',
    'ping_threshold_breached' => '¡Umbral de ping superado!',

    // Run Speedtest Action
    'speedtest' => 'Velocidad',
    'select_server' => 'Seleccionar Servidor',
    'select_server_helper' => 'Dejar en blanco para ejecutar el test de velocidad sin especificar un servidor. Se omitirán los servidores bloqueados.',
    'manual_servers' => 'Servidores manuales',
    'closest_servers' => 'Servidor más cerrado',
    'run_speedtest' => 'Ejecutar prueba de velocidad',
    'start' => 'Empezar',
];
