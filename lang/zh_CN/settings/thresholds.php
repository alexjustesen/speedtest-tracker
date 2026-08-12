<?php

return [
    'title' => '测速阈值',
    'label' => '阈值设置',

    // Absolute thresholds
    'absolute' => '固定阈值',
    'absolute_description' => '固定阈值不会参考历史测速数据，每次测速只要不达标就会触发告警。',
    'absolute_enabled' => '启用固定阈值',

    // Metrics section
    'metrics' => '测速指标',
    'metrics_helper_text' => '填写零代表关闭该指标阈值检测。',

    // General threshold labels
    'thresholds' => '阈值参数',
    'threshold_enabled' => '启用该阈值',
    'threshold_download' => '下载速度阈值',
    'threshold_upload' => '上传速度阈值',
    'threshold_ping' => '延迟阈值',
];
