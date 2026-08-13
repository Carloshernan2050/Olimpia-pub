<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — {{ config('app.name', 'Olimpia') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pagina-olimpia">
    <main class="pantalla-formulario">
        <a class="boton-volver" href="{{ url('/') }}" aria-label="Volver">&#8592;</a>

        <h1>Iniciar Sesión</h1>

        <form class="formulario-olimpia una-columna" method="POST" action="{{ route('iniciar-sesion.guardar') }}">
            @csrf

            @if (session('error'))
                <div class="campo">
                    <div class="error">{{ session('error') }}</div>
                </div>
            @endif

            <div class="campo">
                <label for="correo">Correo Electrónico</label>
                <input id="correo" name="correo" type="email" value="{{ old('correo') }}" maxlength="150" autocomplete="username" required autofocus>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <input id="contrasena" name="contrasena" type="password" autocomplete="current-password" required>
                @error('contrasena') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="acciones-formulario">
                <button class="boton-principal" type="submit">Iniciar Sesión</button>
                <hr class="separador-azul">
                <p class="enlace-secundario">
                    ¿No tienes cuenta?
                    <a href="{{ route('registro') }}">Regístrate</a>
                </p>
            </div>
        </form>
    </main>
</body>
</html>
