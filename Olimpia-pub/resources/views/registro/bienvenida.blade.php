@extends('layouts.olimpia')

@section('titulo', config('app.name', 'Olimpia'))

@section('contenido')
    <main class="pantalla-bienvenida columna-centrada">
        <header>
            <p class="marca">Olimpia Pub</p>
            <h1 class="titulo-olimpia">Bienvenidos</h1>
            @auth
                <p class="saludo">Hola, {{ auth()->user()?->primer_nombre }}</p>
            @endauth
        </header>

        <div class="acciones columna-centrada">
            @guest
                <a class="boton-principal" href="{{ route('iniciar-sesion') }}">Iniciar Sesión</a>
                <a class="boton-principal" href="{{ route('registro') }}">Registrarse</a>
            @else
                <x-dashboard.cerrar-sesion />
            @endguest
        </div>
    </main>
@endsection
