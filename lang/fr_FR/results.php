<?php

return [
    'title' => 'Résultats',
    'result_overview' => 'Aperçu des résultats',

    // Metrics
    'download_latency_high' => 'latence de téléchargement élevée',
    'download_latency_low' => 'latence de téléchargement faible',
    'download_latency_iqm' => 'Télécharger la latence IQM',
    'download_latency_jitter' => 'Télécharger le jitter de latence',

    'upload_latency_high' => 'latence d\'envoi élevée',
    'upload_latency_low' => 'Délai d\'upload faible',
    'upload_latency_iqm' => 'Déposer la latence IQM',
    'upload_latency_jitter' => 'Télécharger le jitter de latence',

    'ping_details' => 'Détails du ping',
    'ping_jitter' => 'Ping jitter',
    'ping_high' => 'Ping haut',
    'ping_low' => 'Ping bas',

    'packet_loss' => 'Perte de paquets',
    'iqm' => 'IQM',

    // Server & metadata
    'server_&_metadata' => 'Serveur & Métadonnées',
    'server_id' => 'ID du serveur',
    'server_host' => 'Hôte du serveur',
    'server_name' => 'Nom du serveur',
    'server_location' => 'Emplacement du serveur',
    'service' => 'Service',
    'isp' => 'FAI',
    'ip_address' => 'Adresse IP',
    'scheduled' => 'Planifié',

    // Filters
    'only_healthy_speedtests' => 'Seulement des tests de vitesse sains',
    'only_unhealthy_speedtests' => 'Seulement des tests de vitesse malsains',
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
    'update_comments' => 'Mettre à jour les commentaires',
    'truncate_results' => 'Tronquer les résultats',
    'truncate_results_description' => 'Êtes-vous sûr de vouloir tronquer tous les résultats ? Cette action est irréversible.',
    'truncate_results_success' => 'Tableau des résultats tronqué !',
    'view_on_speedtest_net' => 'Voir sur Speedtest.net',

    // Notifications
    'speedtest_started' => 'Test de vitesse démarré',
    'speedtest_completed' => 'Test de vitesse terminé',
    'download_threshold_breached' => 'Le seuil de téléchargement a été dépassé !',
    'upload_threshold_breached' => 'Seuil de téléversement dépassé !',
    'ping_threshold_breached' => 'Seuil de ping dépassé !',

    // Run Speedtest Action
    'speedtest' => 'Test de vitesse',
    'public_dashboard' => 'Tableau de bord public',
    'select_server' => 'Sélectionner un serveur',
    'select_server_helper' => 'Laisser vide pour exécuter le test de vitesse sans spécifier de serveur. Les serveurs bloqués seront ignorés.',
    'manual_servers' => 'Serveurs manuels',
    'closest_servers' => 'Serveurs les plus fermés',
];
