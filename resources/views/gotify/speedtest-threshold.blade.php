**Deprecation Notice: The Gotify notification channel will stop working at the end of January 2026. Please migrate to Apprise which supports Gotify and many other services.**

**Speedtest Threshold Breached - #{{ $id }}**

A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

@foreach ($metrics as $item)
- {{ $item['name'] }} {{ $item['threshold'] }}: **{{ $item['value'] }}**
@endforeach
- **Ookla Speedtest:** [{{ $speedtest_url }}]({{ $speedtest_url }})
- **URL:** [{{ $url }}]({{ $url }})
