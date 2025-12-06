<?php

return [
    'title' => 'Résultats',
    'result_overview' => 'Aperçu des résultats',
    'error_message_title' => 'Message d\'erreur',

    // Metrics
    'download' => 'Téléchargement',
    'download_latency_high' => 'Latence de téléchargement élevée',
    'download_latency_low' => 'Latence de téléchargement bas',
    'download_latency_iqm' => 'Latence de téléchargement MIQ',
    'download_latency_jitter' => 'Latence de téléchargement gigue',

    'upload' => 'Envoi',
    'upload_latency_high' => 'Latence d\'envoi élevée',
    'upload_latency_low' => 'Latence d\'envoi faible',
    'upload_latency_iqm' => 'Latence d\'envoi MIQ',
    'upload_latency_jitter' => 'Latence d\'envoi gigue',

    'ping' => 'Latence',
    'ping_details' => 'Détails des latences',
    'ping_jitter' => 'Latence gigue',
    'ping_high' => 'Latence élevée',
    'ping_low' => 'Latence faible',

    'packet_loss' => 'Perte de paquets',
    'iqm' => 'MIQ',

    // Server & metadata
    'server_&_metadata' => 'Serveur et Métadonnées',
    'server_id' => 'Identifiant du serveur',
    'server_host' => 'Hôte du serveur',
    'server_name' => 'Nom du serveur',
    'server_location' => 'Emplacement du serveur',
    'service' => 'Service',
    'isp' => 'FAI',
    'ip_address' => 'Adresse IP',
    'scheduled' => 'Planifié',

    // Filters
    'only_healthy_speedtests' => 'Uniquement les tests de vitesse sains',
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
    'speedtest_benchmark_passed' => 'Le benchmark du test de vitesse a été passé',
    'speedtest_benchmark_failed' => 'Le benchmark du test de vitesse a échoué',
    'speedtest_started' => 'Test de vitesse démarré',
    'speedtest_completed' => 'Test de vitesse terminé',
    'speedtest_failed' => 'Le test de vitesse a échoué',
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
    'run_speedtest' => 'Lancer le test de vitesse',
    'start' => 'Démarrer',
];
