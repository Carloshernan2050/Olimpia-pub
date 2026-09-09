@props([
    'eventoEditar' => null,
])

@php
    $editando = $eventoEditar !== null;
    $accion = $editando
        ? route('eventos.actualizar', $eventoEditar->id)
        : route('eventos.guardar');
    $estados = \App\Enums\EstadoEvento::cases();
    $estadoActual = old('estado', $eventoEditar?->estado ?? \App\Enums\EstadoEvento::Programado->value);
@endphp

<form
    class="formulario-evento"
    method="POST"
    action="{{ $accion }}"
    enctype="multipart/form-data"
    novalidate
>
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="campo">
        <label for="evento-nombre">Nombre</label>
        <input
            id="evento-nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $eventoEditar?->nombre) }}"
            maxlength="150"
            required
        >
        <x-error-campo nombre="nombre" />
    </div>

    <div class="campo">
        <label for="evento-descripcion">Descripción</label>
        <input
            id="evento-descripcion"
            name="descripcion"
            type="text"
            value="{{ old('descripcion', $eventoEditar?->descripcion) }}"
            maxlength="255"
        >
        <x-error-campo nombre="descripcion" />
    </div>

    <div class="formulario-evento-cuando">
        <div class="campo">
            <label for="evento-fecha">Fecha</label>
            <input
                id="evento-fecha"
                name="fecha"
                type="date"
                value="{{ old('fecha', $eventoEditar?->fecha) }}"
                required
            >
            <x-error-campo nombre="fecha" />
        </div>

        <div class="campo">
            <label for="evento-hora">Hora</label>
            <input
                id="evento-hora"
                name="hora"
                type="time"
                value="{{ old('hora', $eventoEditar?->hora) }}"
                required
            >
            <x-error-campo nombre="hora" />
        </div>
    </div>

    <div class="campo">
        <label for="evento-imagen">Imagen</label>
        @if ($eventoEditar?->tieneImagen())
            <img
                class="evento-imagen-actual"
                src="{{ $eventoEditar->urlImagenPublica() }}"
                alt="Imagen actual de {{ $eventoEditar->nombre }}"
            >
        @endif
        <input
            id="evento-imagen"
            name="imagen"
            type="file"
            accept="image/jpeg,image/png,image/webp"
        >
        <x-error-campo nombre="imagen" />
    </div>

    <div class="campo">
        <label for="evento-estado">Estado</label>
        <select id="evento-estado" name="estado">
            @foreach ($estados as $estado)
                <option
                    value="{{ $estado->value }}"
                    @selected($estadoActual === $estado->value)
                >
                    {{ $estado->etiqueta() }}
                </option>
            @endforeach
        </select>
        <x-error-campo nombre="estado" />
    </div>

    <button class="evento-guardar" type="submit">
        {{ $editando ? 'Guardar cambios' : 'Agregar' }}
    </button>
</form>
