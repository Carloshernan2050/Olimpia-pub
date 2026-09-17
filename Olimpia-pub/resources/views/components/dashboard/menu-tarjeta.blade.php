@props(['producto'])

<article class="menu-tarjeta">
    <div class="menu-tarjeta-imagen">
        @if ($producto->tieneImagen())
            <img src="{{ $producto->urlImagenPublica() }}" alt="{{ $producto->nombre }}">
        @else
            <div class="menu-tarjeta-imagen-vacia">
                <x-dashboard.icono nombre="imagen" />
            </div>
        @endif
    </div>

    <div class="menu-tarjeta-cuerpo">
        <div class="menu-tarjeta-textos">
            <h2 class="menu-tarjeta-nombre">{{ $producto->nombre }}</h2>
            <p class="menu-tarjeta-precio">{{ $producto->precioFormateado() }}</p>
        </div>
    </div>
</article>
