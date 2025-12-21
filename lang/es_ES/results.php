<?php

return [
    'title' => 'Resultados',
    'result_overview' => 'Resumen de resultados',
    'error_message_title' => 'Mensaje de error',

    // Metrics
    'download' => 'Descargar',
    'download_latency_high' => 'Descargar latencia alta',
    'download_latency_low' => 'Descargar latencia baja',
    'download_latency_iqm' => 'Descargar latencia IQM',
    'download_latency_jitter' => 'Descargar jitter de latencia',

    'upload' => 'Subir',
    'upload_latency_high' => 'Subir latencia alta',
    'upload_latency_low' => 'Subir latencia baja',
    'upload_latency_iqm' => 'Cargar latencia IQM',
    'upload_latency_jitter' => 'Subir jitter de latencia',

    'ping' => 'Señal',
    'ping_details' => 'Detalles de ping',
    'ping_jitter' => 'Ping jitter',
    'ping_high' => 'Ping alto',
    'ping_low' => 'Ping bajo',

    'packet_loss' => 'Pérdida del paquete',
    'iqm' => 'IQM',

    // Server & metadata
    'server_&_metadata' => 'Servidor y metadatos',
    'server_id' => 'ID del servidor',
    'server_host' => 'Servidor host',
    'server_name' => 'Nombre del servidor',
    'server_location' => 'Ubicación del servidor',
    'service' => 'Servicio',
    'isp' => 'ISP',
    'ip_address' => 'Dirección IP',
    'scheduled' => 'Programado',

    // Filters
    'only_healthy_speedtests' => 'Sólo pruebas de velocidad saludables',
    'only_unhealthy_speedtests' => 'Sólo pruebas de velocidad poco saludables',
    'only_manual_speedtests' => 'Sólo pruebas de velocidad manuales',
    'only_scheduled_speedtests' => 'Sólo pruebas de velocidad programadas',
    'created_from' => 'Creado a partir de',
    'created_until' => 'Creado hasta',

    // Export
    'export_all_results' => 'Exportar todos los resultados',
    'export_all_results_description' => 'Exportará cada columna para todos los resultados.',
    'export_completed' => 'Exportación completada, :count :rows exportadas.',
    'failed_export' => ':count :rows falló al exportar.',
    'row' => '{1} :count fila|[2,*] :count filas',

    // Actions
    'update_comments' => 'Actualizar comentarios',
    'view_on_speedtest_net' => 'Ver en Speedtest.net',

    // Notifications
    'speedtest_benchmark_passed' => 'La prueba de rendimiento de velocidad ha pasado',
    'speedtest_benchmark_failed' => 'Prueba de rendimiento de velocidad fallida',
    'speedtest_started' => 'Velocidad iniciada',
    'speedtest_completed' => 'Velocidad completada',
    'speedtest_failed' => 'Error en la prueba de velocidad',
    'download_threshold_breached' => '¡Umbral de descarga incumplido!',
    'upload_threshold_breached' => '¡Umbral de subida infringido!',
    'ping_threshold_breached' => '¡Umbral de ping infringido!',

    // Run Speedtest Action
    'speedtest' => 'Velocidad',
    'select_server' => 'Seleccionar Servidor',
    'select_server_helper' => 'Dejar en blanco para ejecutar el test de velocidad sin especificar un servidor. Se omitirán los servidores bloqueados.',
    'manual_servers' => 'Servidores manuales',
    'closest_servers' => 'Servidor más cerrado',
    'run_speedtest' => 'Ejecutar prueba de velocidad',
    'start' => 'Empezar',
];
