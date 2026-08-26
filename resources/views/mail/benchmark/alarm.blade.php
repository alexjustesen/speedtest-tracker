<x-mail::message>
# Speedtest Benchmark Alarm - #{{ $id }}

A new speedtest was completed using **{{ $service }}** on **{{ $isp }}**. but a benchmark was breached.:

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
