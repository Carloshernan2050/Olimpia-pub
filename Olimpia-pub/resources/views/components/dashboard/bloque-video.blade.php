@props(['bloque'])

<article class="bloque-inicio bloque-video" data-posicion="{{ $bloque->posicion->value }}">
    <div class="bloque-video-marco">
        <video
            controls
            playsinline
            preload="metadata"
            @if ($bloque->titulo) title="{{ $bloque->titulo }}" @endif
        >
            @if ($bloque->urlMediaPublica())
                <source src="{{ $bloque->urlMediaPublica() }}" type="video/mp4">
            @endif
            <track
                kind="subtitles"
                src="{{ $bloque->urlSubtitulosPublica() ?? asset('media/inicio/portada.es.vtt') }}"
                srclang="es"
                label="Español"
                default
            >
            <track
                kind="descriptions"
                src="{{ $bloque->urlDescripcionPublica() ?? asset('media/inicio/portada.descripcion.es.vtt') }}"
                srclang="es"
                label="Descripción"
            >
            Tu navegador no reproduce video HTML5.
        </video>
    </div>
</article>
