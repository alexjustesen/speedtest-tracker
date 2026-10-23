<?php

return [
    'title' => 'Schedules',
    'label' => 'Schedules',
    'singular' => 'Schedule',

    // Tabs
    'tab_general' => 'General',
    'tab_servers' => 'Servers',

    // Form fields
    'name' => 'Name',
    'name_placeholder' => 'My schedule',
    'enabled' => 'Enabled',
    'schedule' => 'Cron expression',
    'schedule_placeholder' => '0 * * * *',
    'schedule_overlap' => 'A schedule with this cron expression already exists.',
    'schedule_empty' => 'No cron expression provided.',
    'schedule_invalid' => 'The cron expression is invalid.',
    'schedule_unsupported' => 'The cron expression is not supported.',
    'server_id_manual' => 'Server ID',
    'server_mode' => 'Server selection',
    'servers' => 'Preferred servers',
    'servers_helper' => 'Search by server name to find and select preferred servers. When multiple preferred servers are selected, one is chosen at random for each test.',

    // Table columns
    'columns' => [
        'name' => 'Name',
        'enabled' => 'Enabled',
        'schedule' => 'Schedule',
        'next_run_at' => 'Next run at',
        'last_run_at' => 'Last ran at',
        'id' => 'ID',
        'server_mode' => 'Server mode',
        'servers' => 'Servers',
    ],

    // Table filters
    'filter_enabled' => 'Enabled',
    'filter_disabled' => 'Disabled',

    // Table actions
    'action_enable' => 'Enable',
    'action_disable' => 'Disable',

];
