@props([
    'filtro',
    'categorias',
])

<form
    class="inventario-filtro"
    method="GET"
    action="{{ route('inventario') }}"
    data-filtro-inventario
>
    <label class="inventario-filtro-busqueda">
        <span class="sr-only">Buscar</span>
        <x-dashboard.icono nombre="buscar" />
        <input
            type="search"
            name="busqueda"
            value="{{ $filtro->busqueda }}"
            placeholder="Buscar"
            maxlength="150"
        >
    </label>

    <label class="inventario-filtro-select">
        <span class="sr-only">Categoría</span>
        <select name="categoria">
            <option value="">Categoría</option>
            @foreach ($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    @selected($filtro->idCategoria === $categoria->id)
                >
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="inventario-filtro-select">
        <span class="sr-only">Estado de stock</span>
        <select name="estado">
            <option value="">Estado</option>
            @foreach (\App\Enums\EstadoStockInventario::cases() as $estado)
                <option
                    value="{{ $estado->value }}"
                    @selected($filtro->estadoStock === $estado)
                >
                    {{ $estado->etiqueta() }}
                </option>
            @endforeach
        </select>
    </label>

    <button class="sr-only" type="submit">Aplicar filtros</button>
</form>
