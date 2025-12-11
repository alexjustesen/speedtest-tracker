<?php

return [
    'title' => 'Schedules',
    'singular' => 'Schedule',

    // Form labels
    'active' => 'Active',
    'name' => 'Name',
    'name_placeholder' => 'Enter a name for the test.',
    'description' => 'Description',
    'type' => 'Type',

    // Schedule tab
    'schedule' => 'Schedule',
    'schedule_placeholder' => 'Enter a cron expression.',
    'next_run_at' => 'Next Run At',

    // Servers tab
    'servers' => 'Servers',
    'server_preference' => 'Server Preference',
    'server_preference_auto' => 'Automatic selection',
    'server_preference_prefer' => 'Prefered servers',
    'server_preference_ignore' => 'Ignore servers',
    'server_id' => 'Server ID',
    'server_id_placeholder' => 'Enter the ID of the server.',

    // Advanced tab
    'advanced' => 'Advanced',
    'skip_ips' => 'Skip IP addresses',
    'skip_ips_placeholder' => '8.8.8.8',
    'skip_ips_helper' => 'Add external IP addresses that should be skipped.',
    'network_interface' => 'Network Interface',
    'network_interface_placeholder' => 'eth0',
    'network_interface_helper' => 'Set the network interface to use for the test. This need to be the network interface available inside the container',

    // Table columns
    'id' => 'ID',
    'created_by' => 'Created By',
    'status' => 'Status',
    'next_run_at' => 'Next Run At',
    'last_run_at' => 'Last Run At',

    // Filters
    'active_schedules_only' => 'Active schedules only',
    'inactive_schedules_only' => 'Inactive schedules only',

    // Actions
    'change_schedule_status' => 'Change Schedule Status',
    'view_results' => 'View Results',

    // Details section
    'details' => 'Details',
    'options' => 'Options',

    // Notifications
    'overlap_detected' => 'Schedule Overlap Detected',
    'overlap_body' => 'The cron expression for this schedule overlaps with the following active schedule ids: :ids.',
];
