@extends('layouts.dashboard')

@section('titulo', 'Menú — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="catalogo-menu" aria-labelledby="titulo-menu">
        <x-dashboard.menu-encabezado :filtro="$filtro" :categorias="$catalogo->categorias" />

        <x-dashboard.menu-categorias
            :categorias="$catalogo->categorias"
            :filtro="$filtro"
        />

        @if ($catalogo->tieneProductos())
            <ul class="grilla-menu">
                @foreach ($catalogo->enOrden() as $producto)
                    <li>
                        <x-dashboard.menu-tarjeta :producto="$producto" />
                    </li>
                @endforeach
            </ul>
        @else
            <p class="menu-vacio">No hay productos en el menú.</p>
        @endif
    </section>

    <x-dashboard.boton-realizar-pedido />
@endsection
