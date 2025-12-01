<x-mail::message>
# {{ $period }} Speedtest Report

**Period**: {{ $periodLabel }}

---

## Performance Averages

<x-mail::table>
| **Metric**         | **Value**                  |
|:-------------------|---------------------------:|
| Average Download   | {{ App\Helpers\Number::toBitRate(bits: $stats['download_avg'] * 8, precision: 2) }} |
| Average Upload     | {{ App\Helpers\Number::toBitRate(bits: $stats['upload_avg'] * 8, precision: 2) }} |
| Average Ping       | {{ $stats['ping_avg'] }} ms            |
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
| {{ $server['server_name'] }} | {{ $server['count'] }} | {{ App\Helpers\Number::toBitRate(bits: $server['download_avg'] * 8, precision: 2) }} | {{ App\Helpers\Number::toBitRate(bits: $server['upload_avg'] * 8, precision: 2) }} | {{ $server['ping_avg'] }} ms |
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
