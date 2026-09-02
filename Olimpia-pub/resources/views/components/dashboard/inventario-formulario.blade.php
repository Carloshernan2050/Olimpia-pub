@props([
    'movimientoEditar' => null,
    'opcionesProducto' => [],
    'idProductoPrefill' => null,
])

@php
    $editando = $movimientoEditar !== null;
    $accion = $editando
        ? route('inventario.actualizar', $movimientoEditar->id)
        : route('inventario.guardar');
    $productoSeleccionado = old(
        'id_producto',
        $movimientoEditar?->idProducto ?? $idProductoPrefill
    );
@endphp

<form
    class="formulario-inventario"
    method="POST"
    action="{{ $accion }}"
    novalidate
>
    @csrf
    <input type="hidden" name="formulario" value="movimiento">
    @if ($editando)
        @method('PUT')
    @endif

    <div class="campo">
        <label for="inventario-producto">Producto</label>
        <select id="inventario-producto" name="id_producto" required>
            <option value="">Selecciona un producto</option>
            @foreach ($opcionesProducto as $opcion)
                <option
                    value="{{ $opcion->id }}"
                    @selected((string) $productoSeleccionado === (string) $opcion->id)
                >
                    {{ $opcion->nombre }}
                </option>
            @endforeach
        </select>
        <x-error-campo nombre="id_producto" />
    </div>

    <div class="campo">
        <label for="inventario-tipo">Tipo</label>
        <select id="inventario-tipo" name="tipo_movimiento" required>
            @foreach (\App\Enums\TipoMovimientoInventario::cases() as $tipo)
                <option
                    value="{{ $tipo->value }}"
                    @selected(old('tipo_movimiento', $movimientoEditar?->tipo->value ?? 'entrada') === $tipo->value)
                >
                    {{ $tipo->etiqueta() }}
                </option>
            @endforeach
        </select>
        <x-error-campo nombre="tipo_movimiento" />
    </div>

    <div class="campo">
        <label for="inventario-cantidad">Cantidad</label>
        <input
            id="inventario-cantidad"
            name="cantidad"
            type="number"
            min="1"
            step="1"
            value="{{ old('cantidad', $movimientoEditar?->cantidad) }}"
            required
        >
        <x-error-campo nombre="cantidad" />
    </div>

    <button class="inventario-guardar" type="submit">
        {{ $editando ? 'Guardar cambios' : 'Registrar' }}
    </button>
</form>
