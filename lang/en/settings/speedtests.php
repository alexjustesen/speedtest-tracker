<?php

return [
    'title' => 'Speedtests',
    'label' => 'Speedtests',

    // Schedules
    'schedules' => 'Schedules',
    'schedules_description' => 'Speedtests that run on a schedule, every schedule runs its own test.',
    'schedules_description_env' => 'Speedtests that run on a schedule, every schedule runs its own test. The SPEEDTEST_SCHEDULE environment variable (":cron") runs as well.',
    'add_schedule' => 'Add schedule',
    'cron' => 'Schedule',
    'cron_helper' => 'Cron expression, e.g. "0 * * * *" for every hour.',
    'target' => 'Target',
    'target_helper_ookla' => 'Optional, leave empty to let Ookla pick a server.',
    'target_helper_source' => 'Optional, leave empty to pick one of the sources below at random.',
    'enabled' => 'Enabled',
    'disabled' => 'disabled',

    // Sources
    'sources' => 'Sources',
    'sources_description' => 'Used by schedules without a target and suggested in the Speedtest dialog.',
    'iperf3_servers' => 'iPerf3 servers',
    'add_iperf3_server' => 'Add iPerf3 server',
    'file_urls' => 'File URLs',
    'add_file_url' => 'Add file URL',

    // Dialog
    'dialog' => 'Speedtest dialog',
    'default_service' => 'Default service',
    'default_service_helper' => 'Service selected when the Speedtest dialog opens.',

    // Measurement
    'measurement' => 'Measurement',
    'measurement_description' => 'How long and with how many parallel connections each direction is measured.',
    'http_duration' => 'Cloudflare, Yandex and file duration',
    'http_streams' => 'Cloudflare, Yandex and file streams',
    'iperf3_duration' => 'iPerf3 duration',
    'iperf3_streams' => 'iPerf3 streams',
    'seconds' => 'seconds',
];
