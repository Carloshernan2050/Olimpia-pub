<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('titulo', config('app.name', 'Olimpia'))</title>
    @include('layouts.parciales.documento-cabeza', [
        'entradas' => ['resources/css/pedido/app.css', 'resources/js/pedido/app.js'],
    ])
</head>
<body class="pagina-olimpia pagina-pedido">
    <x-aviso-flash />
    <main class="pedido-contenido" id="contenido-principal">
        @yield('contenido')
    </main>
</body>
</html>
