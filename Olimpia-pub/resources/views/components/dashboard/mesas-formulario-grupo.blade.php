@props([
    'grupoEditar' => null,
    'mesasUnibles' => [],
])

@php
    $editando = $grupoEditar !== null;
    $accion = $editando
        ? route('mesas.grupos.actualizar', $grupoEditar->id)
        : route('mesas.grupos.guardar');
    $seleccionadas = array_map(
        intval(...),
        old('mesas', $grupoEditar?->idsMesas() ?? []),
    );
@endphp

<form class="formulario-mesas" method="POST" action="{{ $accion }}" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif
    <input type="hidden" name="formulario" value="grupo">

    <fieldset class="campo mesas-unir-campo">
        <legend>Selecciona las mesas</legend>
        @forelse ($mesasUnibles as $mesa)
            <label class="mesas-unir-opcion">
                <input
                    type="checkbox"
                    name="mesas[]"
                    value="{{ $mesa->id }}"
                    @checked(in_array($mesa->id, $seleccionadas, true))
                >
                <span>{{ $mesa->etiqueta() }}</span>
            </label>
        @empty
            <p class="mesas-unir-vacio">No hay mesas disponibles para unir.</p>
        @endforelse
        <x-error-campo nombre="mesas" />
    </fieldset>

    <button class="mesas-guardar" type="submit">
        {{ $editando ? 'Guardar grupo' : 'Unir mesas' }}
    </button>
</form>
