@extends('layouts.dashboard')

@section('titulo', 'Eventos — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="catalogo-eventos" aria-labelledby="titulo-eventos">
        <x-dashboard.eventos-encabezado :filtro="$filtro" />

        @if ($catalogo->tieneEventos())
            <ul class="grilla-eventos">
                @foreach ($catalogo->enOrden() as $evento)
                    <li>
                        <x-dashboard.evento-tarjeta :evento="$evento" />
                    </li>
                @endforeach
            </ul>
        @else
            <p class="sr-only">No hay eventos disponibles.</p>
        @endif
    </section>

    <x-dashboard.boton-agregar-evento
        :filtro="$filtro"
        :edicion="$eventoEditar !== null"
    />

    <x-dashboard.evento-modal
        :filtro="$filtro"
        :evento-editar="$eventoEditar"
        :eventos-gestion="$eventosGestion"
        :abrir="$abrirModal || $errors->any()"
    />

    @if ($eventoVer !== null)
        <x-dashboard.evento-detalle-modal
            :evento="$eventoVer"
            :filtro="$filtro"
        />
    @endif
@endsection
