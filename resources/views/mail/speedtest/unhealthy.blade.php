<x-mail::message>
# Speedtest Threshold Breached - #{{ $id }}

A new speedtest was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

<x-mail::table>
| **Metric** | **Type** | **Threshold Value** | **Result Value** | **Status** |
|:-----------|:---------|:--------------------|:-----------------|:---------:|
@foreach ($benchmarks as $benchmark)
| {{ $benchmark['metric'] }} | {{ $benchmark['type'] }} | {{ $benchmark['threshold_value'] }} | {{ $benchmark['result_value'] }} | {{ $benchmark['passed'] ? '✅' : '❌' }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$url">
{{ __('general.view') }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
