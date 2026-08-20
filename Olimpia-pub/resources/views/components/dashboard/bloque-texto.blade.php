@props(['bloque'])

<article class="bloque-inicio bloque-texto" data-posicion="{{ $bloque->posicion->value }}">
    @if ($bloque->titulo)
        <h2>{{ $bloque->titulo }}</h2>
    @endif

    @if ($bloque->cuerpo)
        <p>{{ $bloque->cuerpo }}</p>
    @endif
</article>
