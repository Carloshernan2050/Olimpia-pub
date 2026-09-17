@props([
    'filtro',
    'edicion' => false,
])

@if ($edicion)
    <a
        class="mesas-agregar"
        href="{{ route('mesas', [...$filtro->query(), 'nueva' => 1]) }}"
        aria-label="Añadir mesa"
    >
        <x-dashboard.icono nombre="mas" />
    </a>
@else
    <button
        class="mesas-agregar"
        type="button"
        data-abrir-modal-mesa
        aria-label="Añadir mesa"
    >
        <x-dashboard.icono nombre="mas" />
    </button>
@endif
