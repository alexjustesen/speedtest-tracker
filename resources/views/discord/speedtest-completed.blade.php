**Speedtest Completed - #{{ $id }}**

A new speedtest on **{{ app(GeneralSettings::class)->app_name }}** was completed using **{{ $service }}**.

- **Server name:** {{ $serverName }}
- **Server ID:** {{ $serverId }}
- **ISP:** {{ $isp }}
- **Ping:** {{ $ping }}
- **Download:** {{ $download }}
- **Upload:** {{ $upload }}
- **Packet Loss:** {{ $packetLoss }} **%**
- **Ookla Speedtest:** {{ $speedtest_url }}
- **URL:** {{ $url }}
