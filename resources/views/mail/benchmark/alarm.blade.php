<x-mail::message>
@if ($recovered)
# Speedtest Benchmark Recovered - #{{ $id }}
@else
# Speedtest Benchmark Alarm - #{{ $id }}
@endif

A new speedtest was completed using **{{ $service }}** on **{{ $isp }}**. Current benchmark status:

<x-mail::table>
| **Metric** | **Type** | **Benchmark Value** | **Result Value** | **Status** |
|:-----------|:---------|:---------------------|:------------------|:----------:|
@foreach ($benchmarks as $benchmark)
| {{ $benchmark['metric'] }} | {{ $benchmark['type'] }} | {{ $benchmark['benchmark_value'] }} | {{ $benchmark['result_value'] }} | {{ $benchmark['passed'] ? '✅' : '❌' }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$url">
{{ __('general.view') }}
</x-mail::button>
</x-mail::message>
