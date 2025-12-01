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

---

<x-mail::button :url="config('app.url') . '/admin/results'">
View All Results
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
