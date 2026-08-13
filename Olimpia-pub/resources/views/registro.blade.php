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

        <form class="formulario-olimpia dos-columnas" method="POST" action="{{ route('registro.guardar') }}">
            @csrf

            @if (session('error'))
                <div class="campo campo-completo">
                    <div class="error">{{ session('error') }}</div>
                </div>
            @endif

            <div class="campo">
                <label for="primer_nombre">Primer Nombre</label>
                <input id="primer_nombre" name="primer_nombre" type="text" value="{{ old('primer_nombre') }}" maxlength="100" autocomplete="given-name" required>
                @error('primer_nombre') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="segundo_nombre">Segundo Nombre</label>
                <input id="segundo_nombre" name="segundo_nombre" type="text" value="{{ old('segundo_nombre') }}" maxlength="100" autocomplete="additional-name">
                @error('segundo_nombre') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="primer_apellido">Primer Apellido</label>
                <input id="primer_apellido" name="primer_apellido" type="text" value="{{ old('primer_apellido') }}" maxlength="100" autocomplete="family-name" required>
                @error('primer_apellido') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input id="segundo_apellido" name="segundo_apellido" type="text" value="{{ old('segundo_apellido') }}" maxlength="100">
                @error('segundo_apellido') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="correo">Correo Electrónico</label>
                <input id="correo" name="correo" type="email" value="{{ old('correo') }}" maxlength="150" autocomplete="email" required>
                @error('correo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <input id="contrasena" name="contrasena" type="password" autocomplete="new-password" required>
                @error('contrasena') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="campo campo-completo">
                <label for="contrasena_confirmation">Confirmar Contraseña</label>
                <input id="contrasena_confirmation" name="contrasena_confirmation" type="password" autocomplete="new-password" required>
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
