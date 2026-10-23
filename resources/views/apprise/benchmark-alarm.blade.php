A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a benchmark was breached.
### Failed Metrics
@foreach ($metrics as $item)
- **{{ $item['name'] }}**
  - **Benchmark:** {{ $item['benchmark'] }} | **Actual:** {{ $item['value'] }}
@endforeach
### Server Information
- **Server:** {{ $serverName }} (ID: {{ $serverId }})
- **ISP:** {{ $isp }}
### Links
- [View Ookla Results]({{ $speedtest_url }})
- [View Dashboard]({{ $url }})
