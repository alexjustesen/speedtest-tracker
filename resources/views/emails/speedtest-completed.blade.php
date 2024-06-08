<x-mail::message>
# Speedtest Completed - #{{ $id }}

A new speedtest was completed using **{{ $service }}**.

<x-mail::table>
| **Metric**  | **Value**                  |
|:------------|---------------------------:|
| Server name | {{ $serverName }}          |
| Server ID   | {{ $serverId }}            |
| Ping        | {{ $ping }}                |
| Download    | {{ $download }}            |
| Upload      | {{ $upload }}              |
| Packet Loss | {{ $packetLoss }}**%**    |

</x-mail::table>

<x-mail::button :url="$url">
View Results
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
