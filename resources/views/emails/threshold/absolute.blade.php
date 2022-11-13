<x-mail::message>
# Speedtest Result #{{ $id }} - Absolute Threshold Failed

<x-mail::table>
| Name       | Threshold         | Value  |
| ------------- |:-------------:| --------:|
@foreach ($metrics as $item)
    | {{ $item['name'] }} | {{ $item['threshold'] }} | {{ $item['value'] }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$url">
View Results
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
