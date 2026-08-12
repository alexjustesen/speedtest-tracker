<?php

return [
    'title' => '通知设置',
    'label' => '通知',

    // Database notifications
    'database' => '站内通知',
    'database_description' => '通过该渠道发送的通知会显示在页面顶部铃铛图标内。',
    'test_database_channel' => '测试站内通知渠道',

    // Mail notifications
    'mail' => '邮件通知',
    'recipients' => '接收邮箱',
    'test_mail_channel' => '测试邮件通知渠道',

    // Apprise notifications
    'apprise' => 'Apprise推送',
    'enable_apprise_notifications' => '启用 Apprise推送通知',
    'apprise_server' => 'Apprise服务端',
    'apprise_server_url' => 'Apprise服务地址',
    'apprise_server_url_helper' => '你的 Apprise 服务访问地址，地址必须以 /notify 结尾',
    'apprise_verify_ssl' => '校验SSL证书',
    'apprise_channels' => '推送渠道列表',
    'apprise_channel_url' => '服务链接',
    'apprise_hint_description' => 'Apprise支持向90余种平台推送消息，你需要部署Apprise服务端，并在下方配置各平台链接。',
    'apprise_channel_url_helper' => '遵循 Apprise 链接格式，示例：discord://WebhookID/Token、slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => '请先保存配置，再测试推送通知。',
    'test_apprise_channel' => '测试Apprise推送',
    'apprise_channel_url_validation_error' => 'Apprise 链接格式无效，必须使用对应协议前缀（如 discord://、slack://），不能直接填写 http/https 链接。详情请查阅 Apprise 官方文档',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhook推送列表',
    'test_webhook_channel' => '测试Webhook推送渠道',
    'webhook_hint_description' => '通用Webhook接口。如需查看请求示例与接入说明请查阅文档；Discord、Ntfy等平台建议直接使用Apprise渠道。',

    // Common notification messages
    'notify_on_every_speedtest_run' => '每次定时测速完成均发送通知',
    'notify_on_every_speedtest_run_helper' => '每次定时测速结束都会推送通知，仅针对测速正常或未设置阈值的记录',
    'notify_on_threshold_failures' => '定时测速超出阈值时发送告警通知',
    'notify_on_threshold_failures_helper' => '当定时测速结果未达到预设阈值时触发推送告警',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => '测试消息：ping',
            'pong' => '返回消息：pong',
            'received' => '站内测试通知已收到！',
            'sent' => '站内测试通知已发送。',
        ],
        'mail' => [
            'add' => '请先添加接收邮箱！',
            'sent' => '测试邮件通知已发送。',
        ],
        'webhook' => [
            'add' => '请先填写Webhook地址！',
            'sent' => '测试邮件通知已发送。',
            'failed' => 'Webhook推送失败。',
            'payload' => 'Webhook测试通知',
        ],
    ],

    // Helper text
    'threshold_helper_text' => '阈值告警会推送到 /fail 路由地址。',
];
