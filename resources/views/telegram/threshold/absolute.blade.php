{{ $site_name }} - Result *#{{ $id }}*

*Absolute Threshold(s) Failed*
-----
@foreach ($metrics as $item)
Threshold *{{ $item['name'] }}* {{ $item['threshold'] }}: *{{ $item['value'] }}*
@endforeach
