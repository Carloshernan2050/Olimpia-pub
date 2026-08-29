@props(['promocion'])

<article class="promocion-tarjeta">
    <div class="promocion-tarjeta-imagen">
        @if ($promocion->tieneImagen())
            <img src="{{ $promocion->urlImagenPublica() }}" alt="{{ $promocion->nombre }}">
        @else
            <div class="promocion-tarjeta-imagen-vacia">
                <x-dashboard.icono nombre="imagen" />
            </div>
        @endif
    </div>

    <div class="promocion-tarjeta-cuerpo">
        <div class="promocion-tarjeta-textos">
            <h2 class="promocion-tarjeta-nombre">{{ $promocion->nombre }}</h2>
            <p class="promocion-tarjeta-detalle">{{ $promocion->detalle() }}</p>
        </div>

        <x-dashboard.selector-cantidad :nombre="'cantidad-'.$promocion->id" />

        <button class="promocion-tarjeta-comprar" type="button">
            Comprar
        </button>
    </div>
</article>
