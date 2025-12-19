*Deprecation Notice: The Slack notification channel will stop working at the end of January 2026. Please migrate to Apprise which supports Slack and many other services.*

*Speedtest Completed - #{{ $id }}*

A new speedtest on *{{ config('app.name') }}* was completed using *{{ $service }}*.

- *Server name:* {{ $serverName }}
- *Server ID:* {{ $serverId }}
- *ISP:* {{ $isp }}
- *Ping:* {{ $ping }}
- *Download:* {{ $download }}
- *Upload:* {{ $upload }}
- *Packet Loss:* {{ $packetLoss }} *%*
- *Ookla Speedtest:* {{ $speedtest_url }}
- *URL:* {{ $url }}
