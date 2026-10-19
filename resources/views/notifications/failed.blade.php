A speedtest on **{{ config('app.name') }}** using **{{ $service }}** has failed.

@filled($errorMessage)
### Error
{{ $errorMessage }}
@endfilled

@filled($serverId)
@include('notifications.partials.server')
@endfilled
