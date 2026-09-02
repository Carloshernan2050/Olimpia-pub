@props([
    'filtro',
    'movimientoEditar',
    'movimientosGestion',
    'productoVer',
    'movimientosProducto',
    'opcionesProducto',
    'categorias' => [],
    'idProductoPrefill' => null,
    'formularioMovimiento' => false,
    'abrir' => false,
])

@php
    $tituloModal = 'Agregar producto';

    if ($productoVer) {
        $tituloModal = $productoVer->nombre;
    } elseif ($movimientoEditar) {
        $tituloModal = 'Editar movimiento';
    } elseif ($formularioMovimiento) {
        $tituloModal = 'Registrar movimiento';
    }
@endphp

<dialog
    class="modal-inventario"
    data-modal-inventario
    aria-labelledby="titulo-modal-inventario"
    @if ($abrir) data-abrir @endif
>
    <div class="modal-inventario-caja">
        <header class="modal-inventario-cabecera">
            <h2 id="titulo-modal-inventario">{{ $tituloModal }}</h2>
            <button type="button" class="modal-inventario-cerrar" data-cerrar-modal-inventario aria-label="Cerrar">
                <x-dashboard.icono nombre="cerrar" />
            </button>
        </header>

        @if ($productoVer)
            <section class="inventario-detalle" aria-label="Detalle del producto">
                <p><strong>Categoría:</strong> {{ $productoVer->categoria }}</p>
                <p><strong>Stock:</strong> {{ $productoVer->stock }}</p>
                <p><strong>Precio:</strong> {{ $productoVer->precioFormateado() }}</p>
                <p><strong>Estado:</strong> {{ $productoVer->etiquetaEstadoStock() }}</p>
                @if (filled($productoVer->descripcion))
                    <p>{{ $productoVer->descripcion }}</p>
                @endif
            </section>

            @if (count($movimientosProducto) > 0)
                <section class="modal-inventario-listado" aria-label="Movimientos del producto">
                    <h3>Movimientos</h3>
                    <ul>
                        @foreach ($movimientosProducto as $item)
                            <li>
                                <div>
                                    <p>{{ $item->etiquetaTipo() }} · {{ $item->cantidad }}</p>
                                    <span>{{ $item->fecha }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @elseif ($formularioMovimiento)
            <x-dashboard.inventario-formulario
                :movimiento-editar="$movimientoEditar"
                :opciones-producto="$opcionesProducto"
                :id-producto-prefill="$idProductoPrefill"
            />

            @if (count($movimientosGestion) > 0)
                <section class="modal-inventario-listado" aria-label="Movimientos recientes">
                    <h3>Movimientos recientes</h3>
                    <ul>
                        @foreach ($movimientosGestion as $item)
                            <li>
                                <div>
                                    <p>{{ $item->nombreProducto }}</p>
                                    <span>{{ $item->etiquetaTipo() }} · {{ $item->cantidad }} · {{ $item->fecha }}</span>
                                </div>
                                <div class="modal-inventario-item-acciones">
                                    <a
                                        href="{{ route('inventario', [...$filtro->query(), 'editar' => $item->id]) }}"
                                        aria-label="Editar movimiento de {{ $item->nombreProducto }}"
                                    >
                                        <x-dashboard.icono nombre="lapiz" />
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('inventario.eliminar', $item->id) }}"
                                        onsubmit="return confirm({{ \Illuminate\Support\Js::from(
                                            '¿Eliminar el movimiento de '.$item->nombreProducto.'?'
                                        ) }})"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Eliminar movimiento de {{ $item->nombreProducto }}">
                                            <x-dashboard.icono nombre="papelera" />
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @else
            <x-dashboard.inventario-formulario-producto :categorias="$categorias" />
        @endif
    </div>
</dialog>
