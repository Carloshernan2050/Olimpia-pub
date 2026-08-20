@props([
    'itemsNavegacion',
    'seccionActiva',
])

<nav class="dashboard-nav" aria-label="Secciones">
    <ul class="dashboard-nav-lista dashboard-interior">
        @foreach ($itemsNavegacion as $item)
            <li>
                @if ($item->estaDisponible())
                    <a
                        class="dashboard-nav-item @if ($seccionActiva === $item->clave) is-activo @endif"
                        href="{{ route($item->ruta) }}"
                        aria-label="{{ $item->etiqueta }}"
                        @if ($seccionActiva === $item->clave) aria-current="page" @endif
                    >
                        <x-dashboard.icono :nombre="$item->icono" />
                    </a>
                @else
                    <span
                        class="dashboard-nav-item is-inactivo"
                        title="Próximamente"
                        aria-label="{{ $item->etiqueta }} (próximamente)"
                    >
                        <x-dashboard.icono :nombre="$item->icono" />
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
