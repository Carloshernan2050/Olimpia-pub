@props([
    'filtro',
    'edicion' => false,
])

@if ($edicion)
    <a
        class="eventos-agregar"
        href="{{ route('eventos', [...$filtro->query(), 'nueva' => 1]) }}"
        aria-label="Agregar evento"
    >
        <x-dashboard.icono nombre="mas" />
    </a>
@else
    <button
        class="eventos-agregar"
        type="button"
        data-abrir-modal-evento
        aria-label="Agregar evento"
    >
        <x-dashboard.icono nombre="mas" />
    </button>
@endif
