@extends('layouts.dashboard')

@section('titulo', 'Promociones — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="catalogo-promociones" aria-labelledby="titulo-promociones">
        <x-dashboard.promociones-encabezado :filtro="$filtro" />

        @if ($catalogo->tienePromociones())
            <ul class="grilla-promociones">
                @foreach ($catalogo->enOrden() as $promocion)
                    <li>
                        <x-dashboard.promocion-tarjeta :promocion="$promocion" />
                    </li>
                @endforeach
            </ul>
        @else
            <p class="sr-only">No hay promociones disponibles.</p>
        @endif
    </section>

    <x-dashboard.boton-agregar-promocion
        :filtro="$filtro"
        :edicion="$promocionEditar !== null"
    />

    <x-dashboard.promocion-modal
        :filtro="$filtro"
        :promocion-editar="$promocionEditar"
        :promociones-gestion="$promocionesGestion"
        :abrir="$abrirModal || $errors->any()"
    />
@endsection
