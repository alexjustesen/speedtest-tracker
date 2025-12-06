A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

@foreach ($metrics as $item)
- **{{ $item['name'] }}** {{ $item['threshold'] }}: {{ $item['value'] }}
@endforeach
- **Ookla Speedtest:** {{ $speedtest_url }}
- **URL:** {{ $url }}
