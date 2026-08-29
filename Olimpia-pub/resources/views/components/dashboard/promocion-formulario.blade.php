@props([
    'promocionEditar' => null,
])

@php
    $editando = $promocionEditar !== null;
    $accion = $editando
        ? route('promociones.actualizar', $promocionEditar->id)
        : route('promociones.guardar');
@endphp

<form
    class="formulario-promocion"
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
        <label for="promocion-nombre">Nombre</label>
        <input
            id="promocion-nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $promocionEditar?->nombre) }}"
            maxlength="150"
            required
        >
        <x-error-campo nombre="nombre" />
    </div>

    <div class="campo">
        <label for="promocion-descripcion">Descripción</label>
        <input
            id="promocion-descripcion"
            name="descripcion"
            type="text"
            value="{{ old('descripcion', $promocionEditar?->descripcion) }}"
            maxlength="255"
        >
        <x-error-campo nombre="descripcion" />
    </div>

    <div class="campo">
        <label for="promocion-descuento">Descuento (%)</label>
        <input
            id="promocion-descuento"
            name="descuento"
            type="number"
            min="0"
            max="100"
            step="0.01"
            value="{{ old('descuento', $promocionEditar?->descuento) }}"
            required
        >
        <x-error-campo nombre="descuento" />
    </div>

    <div class="formulario-promocion-fechas">
        <div class="campo">
            <label for="promocion-fecha-inicio">Fecha de inicio</label>
            <input
                id="promocion-fecha-inicio"
                name="fecha_inicio"
                type="date"
                value="{{ old('fecha_inicio', $promocionEditar?->fechaInicio) }}"
                required
            >
            <x-error-campo nombre="fecha_inicio" />
        </div>

        <div class="campo">
            <label for="promocion-fecha-fin">Fecha de fin</label>
            <input
                id="promocion-fecha-fin"
                name="fecha_fin"
                type="date"
                value="{{ old('fecha_fin', $promocionEditar?->fechaFin) }}"
                required
            >
            <x-error-campo nombre="fecha_fin" />
        </div>
    </div>

    <div class="campo">
        <label for="promocion-imagen">Imagen</label>
        @if ($promocionEditar?->tieneImagen())
            <img
                class="promocion-imagen-actual"
                src="{{ $promocionEditar->urlImagenPublica() }}"
                alt="Imagen actual de {{ $promocionEditar->nombre }}"
            >
        @endif
        <input
            id="promocion-imagen"
            name="imagen"
            type="file"
            accept="image/jpeg,image/png,image/webp"
        >
        <x-error-campo nombre="imagen" />
    </div>

    <div class="campo">
        <label for="promocion-estado">Estado</label>
        <select id="promocion-estado" name="estado">
            <option
                value="activa"
                @selected(old('estado', $promocionEditar?->estado ?? 'activa') === 'activa')
            >
                Activa
            </option>
            <option
                value="inactiva"
                @selected(old('estado', $promocionEditar?->estado) === 'inactiva')
            >
                Inactiva
            </option>
        </select>
        <x-error-campo nombre="estado" />
    </div>

    <button class="promocion-guardar" type="submit">
        {{ $editando ? 'Guardar cambios' : 'Agregar' }}
    </button>
</form>
