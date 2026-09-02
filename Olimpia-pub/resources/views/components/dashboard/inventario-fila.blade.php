@props([
    'producto',
    'filtro',
])

<tr class="inventario-fila">
    <td>
        <div class="inventario-fila-producto">
            <div class="inventario-fila-imagen" aria-hidden="true">
                <x-dashboard.icono nombre="imagen" />
            </div>
            <span>{{ $producto->nombre }}</span>
        </div>
    </td>
    <td>
        <div class="inventario-fila-detalle">
            <strong>{{ $producto->nombre }}</strong>
            <span>{{ $producto->detalle() }}</span>
        </div>
    </td>
    <td>{{ $producto->categoria }}</td>
    <td>{{ $producto->precioFormateado() }}</td>
    <td>{{ $producto->stock }}</td>
    <td>
        <span class="inventario-estado inventario-estado-{{ $producto->estadoStock->value }}">
            {{ $producto->etiquetaEstadoStock() }}
        </span>
    </td>
    <td>
        <div class="inventario-fila-acciones">
            <a
                class="inventario-accion inventario-accion-ver"
                href="{{ route('inventario', [...$filtro->query(), 'ver' => $producto->id]) }}"
                aria-label="Ver {{ $producto->nombre }}"
            >
                <x-dashboard.icono nombre="ojo" />
            </a>
            <a
                class="inventario-accion inventario-accion-editar"
                href="{{ route('inventario', [...$filtro->query(), 'producto' => $producto->id]) }}"
                aria-label="Registrar movimiento de {{ $producto->nombre }}"
            >
                <x-dashboard.icono nombre="lapiz" />
            </a>
            <form
                method="POST"
                action="{{ route('inventario.producto.eliminar', $producto->id) }}"
                onsubmit="return confirm({{ \Illuminate\Support\Js::from(
                    '¿Eliminar '.$producto->nombre.' del inventario?'
                ) }})"
            >
                @csrf
                @method('DELETE')
                <button
                    class="inventario-accion inventario-accion-eliminar"
                    type="submit"
                    aria-label="Eliminar {{ $producto->nombre }}"
                >
                    <x-dashboard.icono nombre="papelera" />
                </button>
            </form>
        </div>
    </td>
</tr>
