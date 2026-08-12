<?php

return [
    'title' => '测速记录',
    'result_overview' => '测速概览',
    'error_message_title' => '错误信息',

    // Metrics
    'download' => '下载速度',
    'download_latency_high' => '下载最高延迟',
    'download_latency_low' => '下载最低延迟',
    'download_latency_iqm' => '下载四分位均值延迟',
    'download_latency_jitter' => '下载延迟抖动',

    'upload' => '上传速度',
    'upload_latency_high' => '上传最高延迟',
    'upload_latency_low' => '上传最低延迟',
    'upload_latency_iqm' => '上传四分位均值延迟',
    'upload_latency_jitter' => '上传延迟抖动',

    'ping' => '网络延迟',
    'ping_details' => '网络延迟详情',
    'ping_jitter' => '网络延迟抖动',
    'ping_high' => '网络最高延迟',
    'ping_low' => '网络最低延迟',

    'packet_loss' => '丢包率',
    'iqm' => '四分位均值 IQM',

    // Server & metadata
    'server_&_metadata' => '服务器与信息',
    'server_id' => '服务器ID',
    'server_host' => '服务器主机',
    'server_name' => '服务器名称',
    'server_location' => '服务器地区',
    'service' => '测速服务商',
    'isp' => '运营商',
    'ip_address' => '公网IP',
    'scheduled' => '定时任务',

    // Filters
    'only_healthy_speedtests' => '仅显示正常测速',
    'only_unhealthy_speedtests' => '仅显示异常测速',
    'only_manual_speedtests' => '仅手动测速记录',
    'only_scheduled_speedtests' => '仅定时测速记录',
    'created_from' => '起始时间',
    'created_until' => '结束时间',

    // Export
    'export_all_results' => '导出全部记录',
    'export_all_results_description' => '导出所有测速记录的完整字段数据。',
    'export_completed' => '导出完成，共导出 :count 条记录。',
    'failed_export' => '有 :count 条记录导出失败。',
    'row' => '{1} :count 条记录|[2,*] :count 条记录',

    // Actions
    'update_comments' => '修改备注',
    'view_on_speedtest_net' => '前往Speedtest.net查看详情',

    // Notifications
    'speedtest_benchmark_passed' => '测速阈值校验通过',
    'speedtest_benchmark_failed' => '测速未达到预设阈值',
    'speedtest_started' => '测速任务已启动',
    'speedtest_completed' => '测速任务完成',
    'speedtest_failed' => '测速任务失败',
    'download_threshold_breached' => '下载速度低于阈值！',
    'upload_threshold_breached' => '上传速度低于阈值！',
    'ping_threshold_breached' => '网络延迟超出阈值！',

    // Run Speedtest Action
    'speedtest' => '测速',
    'select_server' => '选择测速服务器',
    'select_server_helper' => '留空则自动选择服务器，已屏蔽的服务器会自动跳过。',
    'manual_servers' => '指定服务器',
    'closest_servers' => '就近服务器',
    'run_speedtest' => '执行测速',
    'start' => '开始',
];
