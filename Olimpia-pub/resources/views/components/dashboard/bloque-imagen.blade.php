@props(['bloque'])

<article class="bloque-inicio bloque-imagen" data-posicion="{{ $bloque->posicion->value }}">
    <figure>
        @if ($bloque->urlMediaPublica())
            <img src="{{ $bloque->urlMediaPublica() }}" alt="{{ $bloque->titulo ?? 'Imagen de inicio' }}">
        @else
            <div class="bloque-imagen-vacio">
                <x-dashboard.icono nombre="imagen" />
            </div>
        @endif
    </figure>
</article>
