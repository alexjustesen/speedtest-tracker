<x-mail::message>
# Speedtest Completed - #{{ $id }}

A new speedtest was completed using **{{ $service }}**.

<x-mail::table>
| **Metric**  | **Value**                  |
|:------------|---------------------------:|
| Server name | {{ $serverName }}          |
| Server ID   | {{ $serverId }}            |
| ISP         | {{ $isp }}                 |
| Ping        | {{ $ping }}                |
| Download    | {{ $download }}            |
| Upload      | {{ $upload }}              |
@filled($packetLoss)
| Packet Loss | {{ $packetLoss }} **%**    |
@endfilled


</x-mail::table>

<x-mail::button :url="$url">
View Results
</x-mail::button>

<x-mail::button :url="$speedtest_url">
View Results on Ookla
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
