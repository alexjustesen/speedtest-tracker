<x-mail::message>
# Speedtest Failed - #{{ $id }}

A speedtest attempt on **{{ $service }}** has failed.

<x-mail::table>
| **Metric**  | **Value**                 |
|:------------|---------------------------:|
| Server Name | {{ $serverName ?? 'Unknown' }} |
| Server ID   | {{ $serverId ?? 'Unknown' }}   |
| ISP         | {{ $isp ?? 'Unknown' }}        |
| Failure Reason | {{ $errorMessage ?? 'Unknown error' }} |

</x-mail::table>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>