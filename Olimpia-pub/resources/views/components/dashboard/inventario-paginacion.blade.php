@props([
    'paginacion',
])

@if ($paginacion->hayMasDeUnaPagina())
    <nav class="inventario-paginacion" aria-label="Paginación de inventario">
        @if ($paginacion->anterior)
            <a href="{{ $paginacion->anterior }}" aria-label="Página anterior">
                ‹
            </a>
        @else
            <span aria-disabled="true">‹</span>
        @endif

        <span class="inventario-paginacion-actual" aria-current="page">
            {{ $paginacion->actual }}
        </span>

        @if ($paginacion->siguiente)
            <a href="{{ $paginacion->siguiente }}" aria-label="Página siguiente">
                ›
            </a>
        @else
            <span aria-disabled="true">›</span>
        @endif
    </nav>
@endif
