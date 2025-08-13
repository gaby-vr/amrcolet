<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>
    {!! $page->html !!}
    @foreach ($page->editor_config->getScripts() as $script)
        <script src="{{ $script }}"></script>
    @endforeach
</body>
</html>


