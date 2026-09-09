@props(['evento'])

<article class="evento-tarjeta">
    <div class="evento-tarjeta-imagen">
        @if ($evento->tieneImagen())
            <img src="{{ $evento->urlImagenPublica() }}" alt="{{ $evento->nombre }}">
        @else
            <div class="evento-tarjeta-imagen-vacia">
                <x-dashboard.icono nombre="imagen" />
            </div>
        @endif
    </div>

    <div class="evento-tarjeta-cuerpo">
        <div class="evento-tarjeta-textos">
            <h2 class="evento-tarjeta-nombre">{{ $evento->nombre }}</h2>
            @if (filled($evento->descripcion))
                <p class="evento-tarjeta-detalle">{{ $evento->descripcion }}</p>
            @endif
            <p class="evento-tarjeta-cuando">
                {{ $evento->fechaFormateada() }} · {{ $evento->horaFormateada() }}
            </p>
        </div>

        <a
            class="evento-tarjeta-accion"
            href="{{ route('eventos', ['ver' => $evento->id]) }}"
        >
            Ver Detalles
        </a>
    </div>
</article>
