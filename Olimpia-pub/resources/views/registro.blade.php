<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrarse — {{ config('app.name', 'Olimpia') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pagina-olimpia">
    <main class="pantalla-formulario">
        <a class="boton-volver" href="{{ url('/') }}" aria-label="Volver">&#8592;</a>

        <h1>Registrarse</h1>

        <form class="formulario-olimpia dos-columnas" method="POST" action="{{ route('registro') }}">
            @csrf

            <div class="campo">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required>
                @error('nombre') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="apellido">Apellido</label>
                <input id="apellido" name="apellido" type="text" value="{{ old('apellido') }}" required>
                @error('apellido') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="correo">Correo Electrónico</label>
                <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <input id="contrasena" name="contrasena" type="password" required>
                @error('contrasena') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo campo-completo">
                <label for="contrasena_confirmation">Confirmar Contraseña</label>
                <input id="contrasena_confirmation" name="contrasena_confirmation" type="password" required>
            </div>

            <div class="acciones-formulario">
                <button class="boton-principal" type="submit">Registrarse</button>
                <hr class="separador-azul">
                <p class="enlace-secundario">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('iniciar-sesion') }}">Inicia sesión</a>
                </p>
            </div>
        </form>
    </main>
</body>
</html>
