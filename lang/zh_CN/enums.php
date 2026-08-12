<?php

return [
    // Status enum values
    'status' => [
        'benchmarking' => '正在测速',
        'checking' => '正在检测',
        'completed' => '测速完成',
        'failed' => '测速失败',
        'running' => '运行中',
        'started' => '已启动',
        'skipped' => '已跳过',
        'waiting' => '等待执行',
    ],

    // Service enum values
    'service' => [
        'faker' => '模拟测速',
        'ookla' => 'Ookla测速',
    ],
];
