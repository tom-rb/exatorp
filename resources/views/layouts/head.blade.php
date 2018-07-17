<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('info.brand_name'))</title>

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    @stack('head')
</head>