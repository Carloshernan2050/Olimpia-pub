@props([
    'filtro',
    'edicion' => false,
])

@if ($edicion)
    <a
        class="inventario-agregar"
        href="{{ route('inventario', [...$filtro->query(), 'nueva' => 1]) }}"
        aria-label="Agregar producto"
    >
        <x-dashboard.icono nombre="mas" />
        <span>Agregar producto</span>
    </a>
@else
    <button
        class="inventario-agregar"
        type="button"
        data-abrir-modal-inventario
        aria-label="Agregar producto"
    >
        <x-dashboard.icono nombre="mas" />
        <span>Agregar producto</span>
    </button>
@endif
