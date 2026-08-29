<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('titulo', config('app.name', 'Olimpia'))</title>
    @include('layouts.parciales.documento-cabeza', [
        'entradas' => ['resources/css/registro/app.css', 'resources/js/registro/app.js'],
    ])
</head>
<body class="pagina-olimpia">
    <x-aviso-flash />
    @yield('contenido')
</body>
</html>
