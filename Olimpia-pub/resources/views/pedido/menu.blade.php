@extends('layouts.pedido')

@section('titulo', $mesa->etiqueta().' — Menú')

@section('contenido')
    <section
        class="catalogo-menu"
        aria-labelledby="titulo-menu"
        data-pedido-mesa
        data-pedido-codigo="{{ $mesa->codigo }}"
        @if (session('exito')) data-pedido-exito @endif
    >
        <p class="pedido-mesa-numero">{{ $mesa->etiqueta() }}</p>

        <x-dashboard.menu-encabezado
            :filtro="$filtro"
            :categorias="$mesa->catalogo->categorias"
            ruta="mesa.menu"
            :parametros="['codigo' => $mesa->codigo]"
        />

        <x-dashboard.menu-categorias
            :categorias="$mesa->catalogo->categorias"
            :filtro="$filtro"
            ruta="mesa.menu"
            :parametros="['codigo' => $mesa->codigo]"
        />

        @if ($mesa->catalogo->tieneProductos())
            <ul class="grilla-menu">
                @foreach ($mesa->catalogo->enOrden() as $producto)
                    <li>
                        <x-pedido.tarjeta :producto="$producto" />
                    </li>
                @endforeach
            </ul>
        @else
            <p class="menu-vacio">No hay productos en el menú.</p>
        @endif
    </section>

    <x-pedido.barra :codigo="$mesa->codigo" />
@endsection
