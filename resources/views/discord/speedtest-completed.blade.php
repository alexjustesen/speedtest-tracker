**Speedtest Completed - #{{ $id }}**

A new speedtest with **{{ config('app.name') }}** was completed using **{{ $service }}**.

- **Server name:** {{ $serverName }}
- **Server ID:** {{ $serverId }}
- **ISP:** {{ $isp }}
- **Ping:** {{ $ping }}
- **Download:** {{ $download }}
- **Upload:** {{ $upload }}
- **Packet Loss:** {{ $packetLoss }} **%**
- **Ookla Speedtest:** {{ $speedtest_url }}
- **URL:** {{ $url }}
