# Breaking Changes

This document tracks breaking changes that may affect existing users.

## v2.x - Prometheus Metrics Refactor

**Date**: 2026-02-12
**PR**: #XXXX
**Impact**: High - Affects all users with Prometheus dashboards/alerts

### Overview

The Prometheus metrics implementation has been significantly refactored to follow Prometheus best practices and naming conventions.

### Metric Name Changes

All speed metrics now have explicit `_per_second` suffix to indicate they are rates:

| Old Metric | New Metric | Migration |
|------------|------------|-----------|
| `speedtest_tracker_download_bytes` | `speedtest_tracker_download_bytes_per_second` | Update all dashboard queries and alert rules |
| `speedtest_tracker_upload_bytes` | `speedtest_tracker_upload_bytes_per_second` | Update all dashboard queries and alert rules |
| `speedtest_tracker_download_bits` | `speedtest_tracker_download_bits_per_second` | Update all dashboard queries and alert rules |
| `speedtest_tracker_upload_bits` | `speedtest_tracker_upload_bits_per_second` | Update all dashboard queries and alert rules |
| `speedtest_tracker_downloaded_bytes` | `speedtest_tracker_test_downloaded_bytes_total` | Update all dashboard queries and alert rules |
| `speedtest_tracker_uploaded_bytes` | `speedtest_tracker_test_uploaded_bytes_total` | Update all dashboard queries and alert rules |
| `speedtest_tracker_result_id` | `speedtest_tracker_info` | Update queries - value is now always `1` |

### Label Changes

Some labels have been removed to reduce cardinality:

**Removed Labels**:
- `server_id` - Use `server_name` instead for filtering
- `server_country` - Not essential for most queries
