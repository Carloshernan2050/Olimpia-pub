@props([
    'categorias' => [],
])

<form
    class="formulario-inventario"
    method="POST"
    action="{{ route('inventario.producto.guardar') }}"
    novalidate
>
    @csrf
    <input type="hidden" name="formulario" value="producto">

    <div class="campo">
        <label for="inventario-nombre">Nombre</label>
        <input
            id="inventario-nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre') }}"
            maxlength="150"
            required
        >
        <x-error-campo nombre="nombre" />
    </div>

    <div class="campo">
        <label for="inventario-descripcion">Descripción</label>
        <input
            id="inventario-descripcion"
            name="descripcion"
            type="text"
            value="{{ old('descripcion') }}"
            maxlength="255"
        >
        <x-error-campo nombre="descripcion" />
    </div>

    <div class="formulario-inventario-fila">
        <div class="campo">
            <label for="inventario-precio">Precio</label>
            <input
                id="inventario-precio"
                name="precio"
                type="number"
                min="0"
                step="0.01"
                value="{{ old('precio') }}"
                required
            >
            <x-error-campo nombre="precio" />
        </div>

        <div class="campo">
            <label for="inventario-stock">Stock inicial</label>
            <input
                id="inventario-stock"
                name="stock"
                type="number"
                min="0"
                step="1"
                value="{{ old('stock', 0) }}"
                required
            >
            <x-error-campo nombre="stock" />
        </div>
    </div>

    <div class="campo">
        <label for="inventario-categoria">Categoría</label>
        <select id="inventario-categoria" name="id_categoria" required>
            <option value="">Selecciona una categoría</option>
            @foreach ($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    @selected((string) old('id_categoria') === (string) $categoria->id)
                >
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        <x-error-campo nombre="id_categoria" />
    </div>

    <div class="campo">
        <label for="inventario-estado">Estado</label>
        <select id="inventario-estado" name="estado">
            <option value="activo" @selected(old('estado', 'activo') === 'activo')>
                Activo
            </option>
            <option value="inactivo" @selected(old('estado') === 'inactivo')>
                Inactivo
            </option>
        </select>
        <x-error-campo nombre="estado" />
    </div>

    <button class="inventario-guardar" type="submit">Agregar</button>
</form>
