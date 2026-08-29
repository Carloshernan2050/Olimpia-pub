@php
    $avisos = array_filter([
        ['tipo' => 'exito', 'mensaje' => session('exito'), 'rol' => 'status'],
        ['tipo' => 'error', 'mensaje' => session('error'), 'rol' => 'alert'],
    ], fn (array $aviso): bool => filled($aviso['mensaje']));
@endphp

@if ($avisos !== [])
    <div class="avisos-flash">
        @foreach ($avisos as $aviso)
            <div
                class="aviso-flash aviso-flash-{{ $aviso['tipo'] }}"
                data-aviso
                data-aviso-ms="4000"
                role="{{ $aviso['rol'] }}"
            >
                <p>{{ $aviso['mensaje'] }}</p>
                <button type="button" class="aviso-flash-cerrar" data-cerrar-aviso aria-label="Cerrar aviso">
                    ×
                </button>
            </div>
        @endforeach
    </div>
@endif
