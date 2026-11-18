### Benchmarks
@foreach ($benchmarks as $item)
- {{ $item['passed'] ? '✅' : '❌' }} **{{ $item['name'] }}**: {{ $item['value'] }} (benchmark: {{ $item['benchmark'] }})
@endforeach

