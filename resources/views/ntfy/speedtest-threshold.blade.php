Speedtest Threshold Breached - #{{ $id }}

A new speedtest on **{{ app(GeneralSettings::class)->app_name }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

@foreach ($metrics as $item)
- {{ $item['name'] }} {{ $item['threshold'] }}: {{ $item['value'] }}
@endforeach
- Ookla Speedtest: {{ $speedtest_url }}
- URL: {{ $url }}
