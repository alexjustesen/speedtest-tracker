<?php

return [
    'title' => 'Intégration des données',
    'label' => 'Intégration des données',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Lorsque cette option est activée, tous les nouveaux résultats des tests de vitesse seront également envoyés à InfluxDB.',
    'influxdb_v2_enabled' => 'Activer',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://votre-instance-influxdb',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Seau',
    'influxdb_v2_bucket_placeholder' => 'test-de-vitesse-tracker',
    'influxdb_v2_token' => 'Jeton',
    'influxdb_v2_verify_ssl' => 'Vérifier SSL',

    // Actions
    'test_connection' => 'Tester la connexion',
    'starting_bulk_data_write_to_influxdb' => 'Démarrage de l\'écriture de données en masse sur InfluxDB',
    'sending_test_data_to_influxdb' => 'Envoi de données de test à InfluxDB',

    // Test connection notifications
    'influxdb_test_failed' => 'Échec du test Influxdb',
    'influxdb_test_failed_body' => 'Consultez les journaux pour plus de détails.',
    'influxdb_test_success' => 'Données de test envoyées à Influxdb avec succès',
    'influxdb_test_success_body' => 'Les données de test ont été envoyées à InfluxDB, vérifiez si les données ont été reçues.',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => 'Échec de la construction de l\'écriture sur Influxdb.',
    'influxdb_bulk_write_failed_body' => 'Consultez les journaux pour plus de détails.',
    'influxdb_bulk_write_success' => 'Charge de données en masse terminée sur Influxdb.',
    'influxdb_bulk_write_success_body' => 'Les données ont été envoyées à InfluxDB, vérifiez si les données ont été reçues.',

    // Prometheus
    'prometheus' => 'Prometheus',
    'prometheus_enabled' => 'Activer',
    'prometheus_enabled_helper_text' => 'Lorsque cette option est activée, les métriques pour chaque nouveau test de vitesse seront disponibles au point de terminaison /prometheus.',
    'prometheus_allowed_ips' => 'Adresses IP autorisées',
    'prometheus_allowed_ips_helper' => 'Liste des adresses IP ou des plages CIDR (par exemple, 192.168.1.0/24) autorisés à accéder au point de terminaison des métriques. Laisser vide pour autoriser toutes les IPs.',

    // Common labels
    'org' => 'Org',
    'bucket' => 'Seau',
];
