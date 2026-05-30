<?php

return [
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'url' => 'URL',
    'events' => 'Events',
    'events_helper' => 'Select one or more events this webhook should listen to.',
    'event_groups' => [
        'speedtest' => 'Speedtest',
        'benchmarks' => 'Benchmark',
    ],
    'secret' => 'Signing secret',
    'secret_helper' => 'Optional. When set, the payload is signed and sent in the Signature header.',
    'send_test' => 'Send test',
    'test_queued' => 'Test webhook queued',
    'test_queued_body' => 'The delivery result will appear in your notifications shortly.',
    'test_sent' => 'Test webhook sent',
    'test_failed' => 'Test webhook failed',
];
