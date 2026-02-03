A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}**.

### Results
- **Server:** {{ $serverName }} (ID: {{ $serverId }})
- **ISP:** {{ $isp }}
- **Ping:** {{ $ping }}
- **Download:** {{ $download }}
- **Upload:** {{ $upload }}
@if($packetLoss)
- **Packet Loss:** {{ $packetLoss }}%
@endif

### Links
- [View Ookla Results]({{ $speedtest_url }})
- [View Dashboard]({{ $url }})
