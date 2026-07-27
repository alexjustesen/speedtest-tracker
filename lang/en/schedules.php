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
    'server_id_manual' => 'Server ID',
    'server_mode' => 'Server selection',
    'server_mode_options' => [
        'auto' => 'Automatic server selection',
        'prefer' => 'Only selected servers',
        'block' => 'Exclude selected servers',
    ],
    'servers' => 'Preferred servers',
    'servers_helper' => 'Search by server name to find and select preferred servers. When multiple preferred servers are selected, one is chosen at random for each test.',
    'blocked_servers' => 'Blocked servers',
    'blocked_servers_helper' => 'Search by server name to find and select servers to exclude from auto-selection.',
    'interface' => 'Network interface',
    'interface_helper' => 'Bind the speedtest to a specific network interface. Leave empty to use the system default.',
    'skip_ips' => 'Skip IPs',
    'skip_ips_helper' => 'Comma-separated IPs or CIDR ranges (e.g. 8.8.8.8,8.8.8.0/24). When the external IP matches, the scheduled test is skipped.',

    // Table columns
    'columns' => [
        'name' => 'Name',
        'enabled' => 'Enabled',
        'schedule' => 'Schedule',
        'next_run_at' => 'Next run at',
        'last_run_at' => 'Last ran at',
        'server_mode' => 'Server mode',
        'servers' => 'Servers',
        'interface' => 'Interface',
        'skip_ips' => 'Skip IPs',
    ],

    // Table filters
    'filter_enabled' => 'Enabled',
    'filter_disabled' => 'Disabled',

    // Table actions
    'action_enable' => 'Enable',
    'action_disable' => 'Disable',

];
