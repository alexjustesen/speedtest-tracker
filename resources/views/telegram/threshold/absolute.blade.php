Speedtest Result *#{{ $id }}*
*Absolute Threshold Failed*
@foreach ($metrics as $item)
Threshold *{{ $item['name'] }}* {{ $item['threshold'] }}: *{{ $item['value'] }}* 
@endforeach
