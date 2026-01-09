{{ $period }} Report - {{ $periodLabel }}

Performance Statistics
━━━━━━━━━━━━━━━━━━━━
Download: {{ $stats['download_avg'] }} (Best: {{ $stats['download_max'] }}, Worst: {{ $stats['download_min'] }})
Upload: {{ $stats['upload_avg'] }} (Best: {{ $stats['upload_max'] }}, Worst: {{ $stats['upload_min'] }})
⏱Ping: {{ $stats['ping_avg'] }} (Best: {{ $stats['ping_min'] }}, Worst: {{ $stats['ping_max'] }})

Summary Statistics
━━━━━━━━━━━━━━━━━━━━
Total Tests: {{ $stats['total_tests'] }}
Successful: {{ $stats['successful_tests'] }}
Failed: {{ $stats['failed_tests'] }}
Healthy: {{ $stats['healthy_tests'] }}
Unhealthy: {{ $stats['unhealthy_tests'] }}
