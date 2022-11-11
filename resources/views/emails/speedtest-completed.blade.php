<x-mail::message>
# Speedtest #{{ $id }} completed

A speedtest was successfully run, click the button below to view the results.

<x-mail::button :url="$url">
View Results
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
