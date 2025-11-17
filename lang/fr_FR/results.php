<?php

return [
    'title' => 'Résultats',
    'result_overview' => 'Aperçu des résultats',

    // Metrics
    'download_latency_high' => 'Latence de téléchargement élevée',
    'download_latency_low' => 'Latence de téléchargement bas',
    'download_latency_iqm' => 'Download latency IQM',
    'download_latency_jitter' => 'Download latency jitter',

    'upload_latency_high' => 'Upload latency high',
    'upload_latency_low' => 'Upload latency low',
    'upload_latency_iqm' => 'Upload latency IQM',
    'upload_latency_jitter' => 'Upload latency jitter',

    'ping_details' => 'Ping details',
    'ping_jitter' => 'Ping jitter',
    'ping_high' => 'Ping high',
    'ping_low' => 'Ping low',

    'packet_loss' => 'Packet loss',
    'iqm' => 'IQM',

    // Server & metadata
    'server_&_metadata' => 'Server & Metadata',
    'server_id' => 'Server ID',
    'server_host' => 'Server host',
    'server_name' => 'Server name',
    'server_location' => 'Server location',
    'service' => 'Service',
    'isp' => 'ISP',
    'ip_address' => 'IP address',
    'scheduled' => 'Scheduled',

    // Filters
    'only_healthy_speedtests' => 'Only healthy speedtests',
    'only_unhealthy_speedtests' => 'Uniquement les tests de vitesse ratés',
    'only_manual_speedtests' => 'Uniquement les tests de vitesse manuels',
    'only_scheduled_speedtests' => 'Uniquement les tests de vitesse programmés',
    'created_from' => 'Créé à partir de',
    'created_until' => 'Créé jusqu\'au',

    // Export
    'export_all_results' => 'Exporter tous les résultats',
    'export_all_results_description' => 'Exporte chaque colonne pour tous les résultats.',
    'export_completed' => 'Exportation terminée, :count :rows exportée.',
    'failed_export' => ':count :rows a échoué à l\'exportation.',
    'row' => '{1} :count ligne|[2,*] :count lignes',

    // Actions
    'update_comments' => 'Actualiser les commentaires',
    'truncate_results' => 'Tronquer les résultats',
    'truncate_results_description' => 'Êtes-vous sûr de vouloir tronquer tous les résultats ? Cette action est irréversible.',
    'truncate_results_success' => 'Tableau des résultats tronqué !',
    'view_on_speedtest_net' => 'Voir sur Speedtest.net',

    // Notifications
    'speedtest_started' => 'Test de vitesse démarré',
    'speedtest_completed' => 'Test de vitesse terminé',
    'download_threshold_breached' => 'Seuil de téléchargement dépassé !',
    'upload_threshold_breached' => 'Seuil d\'envoi dépassé !',
    'ping_threshold_breached' => 'Seuil de latence dépassé !',

    // Run Speedtest Action
    'speedtest' => 'Test de vitesse',
    'public_dashboard' => 'Tableau de bord public',
    'select_server' => 'Sélectionner un serveur',
    'select_server_helper' => 'Laisser vide pour exécuter le test de vitesse sans spécifier de serveur. Les serveurs bloqués seront ignorés.',
    'manual_servers' => 'Serveurs manuels',
    'closest_servers' => 'Serveurs les plus proches',
];
