@props([
    'categorias',
    'filtro',
])

<nav class="menu-categorias" aria-label="Categorías del menú">
    <ul class="menu-categorias-lista">
        @foreach ($categorias as $categoria)
            <li>
                <a
                    class="menu-categoria @if ($filtro->idCategoria === $categoria->id) is-activa @endif"
                    href="{{ route('menu', $filtro->queryConCategoria($categoria->id)) }}"
                    aria-label="{{ $categoria->nombre }}"
                    @if ($filtro->idCategoria === $categoria->id) aria-current="true" @endif
                >
                    <x-dashboard.icono :nombre="$categoria->icono" />
                </a>
            </li>
        @endforeach
        <li>
            <a
                class="menu-categoria @if ($filtro->idCategoria === null) is-activa @endif"
                href="{{ route('menu', $filtro->queryConCategoria(null)) }}"
                aria-label="Todas las categorías"
                @if ($filtro->idCategoria === null) aria-current="true" @endif
            >
                <x-dashboard.icono nombre="puntos" />
            </a>
        </li>
    </ul>
</nav>
