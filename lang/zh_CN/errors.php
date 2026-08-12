<?php

return [
    'server_error' => '服务器错误',
    'oops_server_error' => '糟糕，服务器出现异常！',
    'error_message' => '错误详情',
    'error_fetching_servers' => '获取测速服务器列表失败',
    'servers_refreshed_successfully' => '服务器列表刷新成功',
    'copied_to_clipboard' => '已复制至剪贴板',

    // Speedtest specific errors
    'ookla_error' => '读取测速服务器时发生错误，请查看系统日志排查问题。',
    'cron_invalid' => '定时任务表达式格式错误',

    // Status fix command
    'status_fix' => [
        'confirm' => '确认继续执行？',
        'fail' => '指令已终止。',
        'finished' => '✅ 执行完毕！',
        'info_1' => '该命令会校验全部测速记录，依据实测数据修正状态为「已完成」或「测速失败」。',
        'info_2' => '📖 查阅官方文档：https://docs.speedtest-tracker.dev/other/commands',
    ],
];
