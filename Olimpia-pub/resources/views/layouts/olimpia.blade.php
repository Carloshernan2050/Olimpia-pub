<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', config('app.name', 'Olimpia'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pagina-olimpia">
    @yield('contenido')
</body>
</html>
