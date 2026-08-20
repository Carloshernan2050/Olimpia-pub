@extends('layouts.dashboard')

@section('titulo', 'Inicio — '.config('app.name', 'Olimpia'))

@section('contenido')
    <h1 class="sr-only">Inicio</h1>

    <section class="grilla-inicio" aria-label="Textos y videos de inicio">
        @foreach ($portada->bloquesEnOrden() as $bloque)
            <x-dashboard.bloque :bloque="$bloque" />
        @endforeach
    </section>
@endsection
