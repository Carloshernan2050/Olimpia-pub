@props([
    'filtro',
    'categorias',
    'ruta' => 'menu',
    'parametros' => [],
])

<details
    class="menu-filtro-menu @if ($filtro->estaActivo()) is-activo @endif"
    data-cerrar-al-pulsar-fuera
>
    <summary class="menu-filtro" aria-label="Filtrar menú">
        <span>Filtrar</span>
        <x-dashboard.icono nombre="filtro" />
    </summary>

    <form class="menu-filtro-panel" method="GET" action="{{ route($ruta, $parametros) }}">
        <p class="menu-filtro-titulo">Catálogo</p>

        <label class="menu-filtro-campo">
            <span>Buscar</span>
            <input
                type="search"
                name="busqueda"
                value="{{ $filtro->busqueda }}"
                maxlength="150"
                placeholder="Nombre del producto"
            >
        </label>

        <label class="menu-filtro-campo">
            <span>Categoría</span>
            <select name="categoria">
                <option value="">Todas</option>
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

        <div class="menu-filtro-acciones">
            <button class="menu-filtro-aplicar" type="submit">Aplicar</button>
            <a class="menu-filtro-limpiar" href="{{ route($ruta, $parametros) }}">Limpiar</a>
        </div>
    </form>
</details>
