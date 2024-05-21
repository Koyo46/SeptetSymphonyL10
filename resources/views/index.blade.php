<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SeptetSymphony</title>

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/ts/route.tsx'])

    </head>

    <body class="antialiased">
        <div id="app"></div>
    </body>
</html>