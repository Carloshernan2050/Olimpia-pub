<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('titulo', config('app.name', 'Olimpia'))</title>
    @include('layouts.parciales.documento-cabeza', [
        'entradas' => ['resources/css/dashboard/app.css', 'resources/js/dashboard/app.js'],
    ])
</head>
<body class="pagina-olimpia pagina-dashboard">
    <x-dashboard.cabecera
        :acciones-cabecera="$accionesCabecera"
        :nombre-usuario="$nombreUsuario"
    />

    <x-dashboard.navegacion
        :items-navegacion="$itemsNavegacion"
        :seccion-activa="$seccionActiva"
    />

    <x-aviso-flash />

    <main class="dashboard-contenido" id="contenido-principal">
        @yield('contenido')
    </main>
</body>
</html>
