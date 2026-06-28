<?php

return [
    'title' => 'General',
    'label' => 'General',

    // Display section
    'display' => 'Display',
    'display_timezone' => 'Timezone',
    'display_timezone_helper_text' => 'The timezone used to display dates and times.',
    'datetime_format' => 'Datetime format',
    'datetime_format_helper_text' => 'The format used to display dates and times in tables and lists.',
    'chart_datetime_format' => 'Chart datetime format',
    'chart_datetime_format_helper_text' => 'The format used to display dates on chart axes.',
    'datetime_format_docs' => 'Format options',
    'chart_begin_at_zero' => 'Begin charts at zero',

    // Charts section
    'charts' => 'Charts',
    'default_chart_range' => 'Default chart range',
    'default_chart_range_24h' => 'Last 24 hours',
    'default_chart_range_week' => 'Last week',
    'default_chart_range_month' => 'Last month',

    // Data retention section
    'data_retention' => 'Data Retention',
    'prune_results_older_than' => 'Prune results older than',
    'prune_results_older_than_hint' => 'days',
    'prune_results_older_than_helper_text' => 'Automatically delete results older than this many days. Set to 0 to disable.',

    // Connectivity section
    'connectivity' => 'Connectivity',
    'speedtest_external_ip_url' => 'External IP URL',
    'speedtest_external_ip_url_helper_text' => 'URL used to fetch the external IP address before running a speedtest.',
    'speedtest_internet_check_hostname' => 'Internet check hostname',
    'speedtest_internet_check_hostname_helper_text' => 'Hostname pinged to verify internet connectivity before running a speedtest.',
];
