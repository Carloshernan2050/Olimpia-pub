@props([
    'grupos' => [],
])

@php
    $seleccionados = array_map(
        intval(...),
        old('grupos', []),
    );
@endphp

<form class="formulario-mesas" method="POST" action="{{ route('mesas.grupos.liberar') }}" novalidate>
    @csrf
    <input type="hidden" name="formulario" value="liberar">

    <fieldset class="campo mesas-unir-campo">
        <legend>Selecciona los grupos</legend>
        @forelse ($grupos as $grupo)
            <label class="mesas-unir-opcion">
                <input
                    type="checkbox"
                    name="grupos[]"
                    value="{{ $grupo->id }}"
                    @checked(in_array($grupo->id, $seleccionados, true))
                >
                <span>{{ $grupo->etiqueta() }}</span>
            </label>
        @empty
            <p class="mesas-unir-vacio">No hay mesas unidas para liberar.</p>
        @endforelse
        <x-error-campo nombre="grupos" />
    </fieldset>

    <button class="mesas-guardar" type="submit">
        Liberar mesas
    </button>
</form>
