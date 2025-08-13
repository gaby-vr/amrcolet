<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['title'] ?? __('Comanda amrcolet') }}</title>
</head>
<body>
    <h1>{{ $details['title'] ?? '' }}</h1>
    <p>{!! $details['body'] !!}</p>
</body>
</html>