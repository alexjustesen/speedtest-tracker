<?php

return [
    'title' => 'Results',
    'result_overview' => 'Result overview',

    // Metrics
    'download_latency_high' => 'Download latency high',
    'download_latency_low' => 'Download latency low',
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
    'only_unhealthy_speedtests' => 'Only unhealthy speedtests',
    'only_manual_speedtests' => 'Only manual speedtests',
    'only_scheduled_speedtests' => 'Only scheduled speedtests',
    'created_from' => 'Created from',
    'created_until' => 'Created until',

    // Export
    'export_all_results' => 'Export all results',
    'export_all_results_description' => 'Will export every column for all results.',
    'export_completed' => 'Export completed, :count :rows exported.',
    'failed_export' => ':count :rows failed to export.',
    'row' => '{1} :count row|[2,*] :count rows',

    // Actions
    'update_comments' => 'Update comments',
    'truncate_results' => 'Truncate results',
    'truncate_results_description' => 'Are you sure you want to truncate all results? This action is irreversible.',
    'truncate_results_success' => 'Results table truncated!',
    'view_on_speedtest_net' => 'View on Speedtest.net',

    // Notifications
    'speedtest_started' => 'Speedtest started',
    'speedtest_completed' => 'Speedtest completed',
    'download_threshold_breached' => 'Download threshold breached!',
    'upload_threshold_breached' => 'Upload threshold breached!',
    'ping_threshold_breached' => 'Ping threshold breached!',

    // Run Speedtest Action
    'speedtest' => 'Speedtest',
    'public_dashboard' => 'Public Dashboard',
    'select_server' => 'Select Server',
    'select_server_helper' => 'Leave empty to run the speedtest without specifying a server. Blocked servers will be skipped.',
    'manual_servers' => 'Manual servers',
    'closest_servers' => 'Closest servers',
];
