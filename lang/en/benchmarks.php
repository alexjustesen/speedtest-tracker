<?php

return [
    'benchmark' => 'Benchmark',
    'benchmarks' => 'Benchmarks',

    'metric' => 'Metric',
    'type' => 'Type',
    'state' => 'State',
    'state_changed_at' => 'State Changed At',
    'benchmark_value' => 'Benchmark Value',
    'enable_requires_value' => 'Configure a value before enabling this benchmark.',

    'absolute_value' => 'Value',
    'absolute_value_helper' => 'The speedtest must meet this value to pass.',

    'relative' => 'Relative Baseline',
    'baseline_value' => 'ISP Baseline',
    'baseline_value_helper' => 'The value your ISP promises for this metric.',
    'relative_percentage' => 'Percentage',
    'relative_percentage_helper' => 'The speedtest must reach this percentage of the ISP baseline to pass.',

    'debounce' => 'Debounce',
    'consecutive_breaches' => 'Consecutive Breaches',
    'consecutive_breaches_helper' => 'Number of consecutive scheduled speedtests that must fail before an alarm notification is sent.',
    'repeat_while_in_alarm' => 'Keep notifying while in alarm',
    'repeat_while_in_alarm_helper' => 'When enabled, a notification is sent on every subsequent failing test while already in alarm, not just once.',
];
