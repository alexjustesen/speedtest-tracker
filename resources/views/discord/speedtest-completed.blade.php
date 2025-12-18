**Deprecation Notice: The Discord notification channel is deprecated and will be removed in a future release. Please migrate to Apprise which supports Discord and many other services.**

**Speedtest Completed - #{{ $id }}**

A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}**.

- **Server name:** {{ $serverName }}
- **Server ID:** {{ $serverId }}
- **ISP:** {{ $isp }}
- **Ping:** {{ $ping }}
- **Download:** {{ $download }}
- **Upload:** {{ $upload }}
- **Packet Loss:** {{ $packetLoss }} **%**
- **Ookla Speedtest:** {{ $speedtest_url }}
- **URL:** {{ $url }}
