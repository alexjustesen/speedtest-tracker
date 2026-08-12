<?php

return [
    'title' => '数据集成',
    'label' => '数据集成',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2 数据库',
    'influxdb_v2_description' => '开启后，所有新生成的测速记录会同步推送到 InfluxDB。',
    'influxdb_v2_enabled' => '启用',
    'influxdb_v2_url' => '服务地址',
    'influxdb_v2_url_placeholder' => 'http://你的InfluxDB服务地址',
    'influxdb_v2_org' => '组织',
    'influxdb_v2_bucket' => '存储桶',
    'influxdb_v2_bucket_placeholder' => 'speedtest-tracker',
    'influxdb_v2_token' => '访问令牌',
    'influxdb_v2_verify_ssl' => '校验SSL证书',

    // Actions
    'test_connection' => '测试连接',
    'starting_bulk_data_write_to_influxdb' => '开始批量写入历史数据至 InfluxDB',
    'sending_test_data_to_influxdb' => '正在向 InfluxDB 发送测试数据',

    // Test connection notifications
    'influxdb_test_failed' => 'InfluxDB 连接测试失败',
    'influxdb_test_failed_body' => '查看系统日志获取详细报错信息。',
    'influxdb_test_success' => '测试数据发送至 InfluxDB 成功',
    'influxdb_test_success_body' => '测试数据已推送，请前往 InfluxDB 核对是否收到数据。',

    // Bulk write notifications
    'influxdb_bulk_write_failed' => '批量写入 InfluxDB 数据失败。',
    'influxdb_bulk_write_failed_body' => '查看系统日志获取详细报错信息。',
    'influxdb_bulk_write_success' => '历史数据批量写入 InfluxDB 完成。',
    'influxdb_bulk_write_success_body' => '所有数据已推送，请前往 InfluxDB 核对是否收到数据。',

    // Prometheus
    'prometheus' => 'Prometheus 监控',
    'prometheus_enabled' => '启用',
    'prometheus_enabled_helper_text' => '开启后，每次测速产生的指标将暴露于 /prometheus 接口地址。',
    'prometheus_allowed_ips' => '允许访问的IP地址',
    'prometheus_allowed_ips_helper' => '填写 IP 或 CIDR 网段（例如 192.168.1.0/24），仅名单内地址可访问指标接口。留空则允许全部 IP 访问。',

    // Common labels
    'org' => '组织',
    'bucket' => '存储桶',
];
