<?php

return [
    'title' => 'General',
    'label' => 'General',

    // Charts section
    'charts' => 'Charts',
    'default_chart_range' => 'Default chart range (days)',
    'default_chart_range_helper_text' => 'The number of days shown by default when a chart first loads.',

    // Connectivity section
    'connectivity' => 'Connectivity',
    'external_ip_url' => 'External IP URL',
    'external_ip_url_helper_text' => 'URL that returns your public IP address. Only called when a schedule has skip IPs configured, or as an HTTP fallback when the internet check ping fails.',
    'internet_check_hostname' => 'Internet check hostname',
    'internet_check_hostname_helper_text' => 'Hostname that is pinged before each speedtest to verify there is an internet connection.',
];
