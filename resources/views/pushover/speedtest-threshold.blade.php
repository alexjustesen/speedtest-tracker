<b>Speedtest Threshold Breached - #{{ $id }}</b>

A new speedtest was completed using <b>{{ $service }}</b> on <b>{{ $isp }}</b> but a threshold was breached.

@foreach ($metrics as $item)
- <b>{{ $item['name'] }}</b> {{ $item['threshold'] }}: {{ $item['value'] }}
@endforeach
- <b>Ookla Speedtest:</b> {{ $speedtest_url }}
- <b>URL:</b> {{ $url }}
