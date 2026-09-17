@props([
    'mesaEditar' => null,
    'tipos',
    'siguienteNumero' => 1,
])

@php
    $editando = $mesaEditar !== null;
    $accion = $editando
        ? route('mesas.actualizar', $mesaEditar->id)
        : route('mesas.guardar');
@endphp

<form class="formulario-mesas" method="POST" action="{{ $accion }}" novalidate>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif
    <input type="hidden" name="formulario" value="mesa">

    <div class="campo">
        <label for="mesa-numero">Número</label>
        <input
            id="mesa-numero"
            name="numero_mesa"
            type="number"
            min="1"
            max="9999"
            step="1"
            value="{{ old('numero_mesa', $mesaEditar?->numero ?? $siguienteNumero) }}"
            required
        >
        <x-error-campo nombre="numero_mesa" />
    </div>

    <div class="campo">
        <label for="mesa-tipo">Tipo</label>
        <select id="mesa-tipo" name="tipo" required>
            @foreach ($tipos as $tipo)
                <option
                    value="{{ $tipo->tipo->value }}"
                    @selected(old('tipo', $mesaEditar?->tipo->value) === $tipo->tipo->value)
                >
                    {{ $tipo->etiqueta }}
                </option>
            @endforeach
        </select>
        <x-error-campo nombre="tipo" />
    </div>

    <button class="mesas-guardar" type="submit">
        {{ $editando ? 'Guardar cambios' : 'Añadir mesa' }}
    </button>
</form>
