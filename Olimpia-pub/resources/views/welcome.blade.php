<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Olimpia') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pagina-olimpia">
    <main class="pantalla-bienvenida">
        <header>
            <p class="marca">Olimpia Pub</p>
            <h1>Bienvenidos</h1>
            @auth
                <p class="saludo">Hola, {{ auth()->user()->nombre }}</p>
            @endauth
        </header>

        <div class="acciones">
            @guest
                <a class="boton-principal" href="{{ route('iniciar-sesion') }}">Iniciar Sesión</a>
                <a class="boton-principal" href="{{ route('registro') }}">Registrarse</a>
            @else
                <form method="POST" action="{{ route('cerrar-sesion') }}">
                    @csrf
                    <button class="boton-principal" type="submit">Cerrar Sesión</button>
                </form>
            @endguest
        </div>
    </main>
</body>
</html>
