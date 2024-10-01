<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="https://www.iconpacks.net/icons/2/free-check-icon-3278-thumb.png"
        sizes="32x32">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('/css/font.css') }}">
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased" class="body">
    @inertia
</body>

<style>
    * {
        font-family: 'Fira Sans', sans-serif;
        text-decoration: none;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        cursor: url('https://cdn-icons-png.flaticon.com/512/85/85064.png'), auto;

    }


</style>

</html>
