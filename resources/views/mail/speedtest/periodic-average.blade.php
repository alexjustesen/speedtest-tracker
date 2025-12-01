<x-mail::message>
# {{ $period }} Speedtest Report

**Period**: {{ $periodLabel }}

---

## Performance Statistics

<x-mail::table>
| **Metric**   | **Average** | **Best** | **Worst** |
|:-------------|------------:|------------:|-----------:|
| Download     | {{ $stats['download_avg'] }} | {{ $stats['download_max'] }} | {{ $stats['download_min'] }} |
| Upload       | {{ $stats['upload_avg'] }} | {{ $stats['upload_max'] }} | {{ $stats['upload_min'] }} |
| Ping         | {{ $stats['ping_avg'] }} | {{ $stats['ping_min'] }} | {{ $stats['ping_max'] }} |
| Packet Loss  | {{ $stats['packet_loss_avg'] }} | {{ $stats['packet_loss_max'] }} | {{ $stats['packet_loss_min'] }} |
</x-mail::table>

---

## Summary Statistics

<x-mail::table>
| **Metric**         | **Value**                  |
|:-------------------|---------------------------:|
| Total Tests        | {{ $stats['total_tests'] }}             |
| Successful Tests   | {{ $stats['successful_tests'] }}        |
| Failed Tests       | {{ $stats['failed_tests'] }}            |
| Healthy Tests      | {{ $stats['healthy_tests'] }}           |
| Unhealthy Tests    | {{ $stats['unhealthy_tests'] }}         |
</x-mail::table>

@if($serverStats && $serverStats->isNotEmpty())

---

## Per-Server Averages

<x-mail::table>
| **Server Name** | **Tests** | **Download** | **Upload** | **Ping** |
|:----------------|----------:|-------------:|-----------:|---------:|
@foreach($serverStats as $server)
| {{ $server['server_name'] }} | {{ $server['count'] }} | {{ $server['download_avg'] }} | {{ $server['upload_avg'] }} | {{ $server['ping_avg'] }} |
@endforeach
</x-mail::table>

@endif

---

<x-mail::button :url="config('app.url') . '/admin/results'">
View All Results
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
