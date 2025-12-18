*Deprecation Notice: The Slack notification channel is deprecated and will be removed in a future release. Please migrate to Apprise which supports Slack and many other services.*

**Speedtest Threshold Breached - #{{ $id }}**

A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

@foreach ($metrics as $item)
- *{{ $item['name'] }}* {{ $item['threshold'] }}: {{ $item['value'] }}
@endforeach
- *Ookla Speedtest:* {{ $speedtest_url }}
- *URL:* {{ $url }}
