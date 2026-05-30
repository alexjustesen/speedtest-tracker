<?php

return [
    'title' => 'Schedules',
    'label' => 'Schedules',
    'singular' => 'Schedule',

    // Tabs
    'tab_general' => 'General',
    'tab_servers' => 'Servers',
    'tab_network' => 'Network',

    // Form fields
    'name' => 'Name',
    'name_placeholder' => 'My schedule',
    'enabled' => 'Enabled',
    'schedule' => 'Cron expression',
    'schedule_placeholder' => '0 * * * *',
    'schedule_hint' => 'minute hour day month weekday',
    'schedule_helper' => 'Enter a cron expression to define when speedtests run.',
    'cron_invalid' => 'Invalid cron expression.',
    'schedule_overlap' => 'A schedule with this cron expression already exists.',
    'cron_every_minute' => 'Runs every minute.',
    'cron_every_n_minutes' => 'Runs every :n minutes.',
    'cron_every_hour' => 'Runs every hour.',
    'cron_every_n_hours' => 'Runs every :n hours.',
    'cron_daily_at' => 'Runs daily at :time.',
    'cron_weekly_on' => 'Runs every :day at :time.',
    'cron_monthly_on' => 'Runs monthly on day :day at :time.',
    'cron_custom' => 'Custom schedule.',
    'server_mode' => 'Server selection',
    'server_mode_options' => [
        'auto' => 'Auto-select',
        'prefer' => 'Prefer specific servers',
        'block' => 'Block specific servers',
    ],
    'servers' => 'Preferred server IDs',
    'servers_helper' => 'Comma-separated Ookla server IDs to randomly pick from (e.g. 12345,67890).',
    'blocked_servers' => 'Blocked server IDs',
    'blocked_servers_helper' => 'Comma-separated Ookla server IDs to exclude from auto-selection.',
    'interface' => 'Network interface',
    'interface_placeholder' => 'eth0',
    'interface_helper' => 'Bind the speedtest to a specific network interface. Leave empty to use the system default.',
    'skip_ips' => 'Skip IPs',
    'skip_ips_helper' => 'Comma-separated IPs or CIDR ranges (e.g. 1.2.3.4,10.0.0.0/8). When the external IP matches, the scheduled test is skipped.',

    // Table columns
    'columns' => [
        'name' => 'Name',
        'enabled' => 'Enabled',
        'schedule' => 'Schedule',
        'server_mode' => 'Server mode',
        'servers' => 'Servers',
        'interface' => 'Interface',
        'skip_ips' => 'Skip IPs',
    ],
];
