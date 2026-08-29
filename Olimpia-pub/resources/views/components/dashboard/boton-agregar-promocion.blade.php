@props([
    'filtro',
    'edicion' => false,
])

@if ($edicion)
    <a
        class="promociones-agregar"
        href="{{ route('promociones', [...$filtro->query(), 'nueva' => 1]) }}"
        aria-label="Agregar promoción"
    >
        <x-dashboard.icono nombre="mas" />
    </a>
@else
    <button
        class="promociones-agregar"
        type="button"
        data-abrir-modal-promocion
        aria-label="Agregar promoción"
    >
        <x-dashboard.icono nombre="mas" />
    </button>
@endif
