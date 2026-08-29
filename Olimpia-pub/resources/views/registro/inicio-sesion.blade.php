@extends('layouts.olimpia')

@section('titulo', 'Iniciar sesión — '.config('app.name', 'Olimpia'))

@section('contenido')
    <main class="pantalla-formulario columna-centrada">
        <a class="boton-volver" href="{{ url('/') }}" aria-label="Volver">&#8592;</a>

        <h1 class="titulo-olimpia">Iniciar Sesión</h1>

        <form class="formulario-olimpia una-columna" method="POST" action="{{ route('iniciar-sesion.guardar') }}">
            @csrf

            <div class="campo">
                <label for="correo">Correo Electrónico</label>
                <input
                    id="correo"
                    name="correo"
                    type="email"
                    value="{{ old('correo') }}"
                    maxlength="150"
                    autocomplete="username"
                    required
                    autofocus
                >
                <x-error-campo nombre="correo" />
            </div>

            <div class="campo">
                <label for="contrasena">Contraseña</label>
                <input
                    id="contrasena"
                    name="contrasena"
                    type="password"
                    autocomplete="current-password"
                    required
                >
                <x-error-campo nombre="contrasena" />
            </div>

            <div class="acciones-formulario columna-centrada">
                <button class="boton-principal" type="submit">Iniciar Sesión</button>
                <hr class="separador-azul">
                <p class="enlace-secundario">
                    ¿No tienes cuenta?
                    <a href="{{ route('registro') }}">Regístrate</a>
                </p>
            </div>
        </form>
    </main>
@endsection
