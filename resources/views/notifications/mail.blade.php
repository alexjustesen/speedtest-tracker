<x-mail::message>
# {{ $title }}

{!! $body !!}

@filled($speedtestUrl)
[View Ookla Results]({{ $speedtestUrl }})
@endfilled

<x-mail::button :url="$url">
{{ __('general.view') }}
</x-mail::button>
</x-mail::message>
