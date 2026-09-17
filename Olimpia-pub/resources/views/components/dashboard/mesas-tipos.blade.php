@props([
    'tipos',
    'filtro',
])

<nav class="mesas-tipos" aria-label="Tipos de mesa">
    <ul class="mesas-tipos-lista">
        @foreach ($tipos as $tipo)
            @php
                $activo = $filtro->tipo === $tipo->tipo;
                $etiqueta = $tipo->etiquetaFiltro($activo);
            @endphp
            <li>
                <a
                    class="mesas-tipo {{ $tipo->tipo->clase() }} @if ($activo) is-activa @endif"
                    href="{{ route('mesas', $filtro->queryConTipo($activo ? null : $tipo->tipo)) }}"
                    aria-label="{{ $etiqueta }}"
                    @if ($activo) aria-current="true" @endif
                >
                    <span class="mesas-tipo-marca">
                        <x-dashboard.icono :nombre="$tipo->icono" />
                        <span class="mesas-tipo-nombre">{{ $etiqueta }}</span>
                    </span>
                    <x-dashboard.icono nombre="lineas" />
                </a>
            </li>
        @endforeach
    </ul>
</nav>
