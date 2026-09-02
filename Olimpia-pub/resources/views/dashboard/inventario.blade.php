@extends('layouts.dashboard')

@section('titulo', 'Inventario — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="catalogo-inventario" aria-labelledby="titulo-inventario">
        <x-dashboard.inventario-encabezado />

        <x-dashboard.inventario-resumen :resumen="$catalogo->resumen" />

        <div class="inventario-panel">
            <x-dashboard.inventario-filtro
                :filtro="$filtro"
                :categorias="$catalogo->categorias"
            />

            @if ($catalogo->tieneProductos())
                <x-dashboard.inventario-tabla
                    :productos="$catalogo->enOrden()"
                    :filtro="$filtro"
                />
            @else
                <p class="inventario-vacio">No hay productos en el inventario.</p>
            @endif

            <x-dashboard.inventario-paginacion :paginacion="$catalogo->paginacion" />
        </div>
    </section>

    <x-dashboard.boton-agregar-inventario
        :filtro="$filtro"
        :edicion="$movimientoEditar !== null || $productoVer !== null"
    />

    <x-dashboard.inventario-modal
        :filtro="$filtro"
        :movimiento-editar="$movimientoEditar"
        :movimientos-gestion="$movimientosGestion"
        :producto-ver="$productoVer"
        :movimientos-producto="$movimientosProducto"
        :opciones-producto="$catalogo->opcionesProducto"
        :categorias="$catalogo->categorias"
        :id-producto-prefill="$idProductoPrefill"
        :formulario-movimiento="$formularioMovimiento"
        :abrir="$abrirModal || $errors->any()"
    />
@endsection
