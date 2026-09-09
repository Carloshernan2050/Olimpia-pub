@props([
    'evento',
    'filtro',
])

<dialog
    class="modal-evento-detalle"
    data-modal-evento-detalle
    aria-labelledby="titulo-evento-detalle"
    data-abrir
>
    <article class="evento-ficha">
        <header class="evento-ficha-cabecera">
            <h2 class="evento-ficha-nombre" id="titulo-evento-detalle">{{ $evento->tarjeta->nombre }}</h2>
            <a
                class="evento-ficha-cerrar"
                href="{{ route('eventos', $filtro->query()) }}"
                aria-label="Cerrar"
                data-cerrar-modal-evento-detalle
            >
                <x-dashboard.icono nombre="cerrar" />
            </a>
        </header>

        <div class="evento-tarjeta-imagen">
            @if ($evento->tarjeta->tieneImagen())
                <img src="{{ $evento->tarjeta->urlImagenPublica() }}" alt="{{ $evento->tarjeta->nombre }}">
            @else
                <div class="evento-tarjeta-imagen-vacia">
                    <x-dashboard.icono nombre="imagen" />
                </div>
            @endif
        </div>

        <div class="evento-ficha-cuerpo">
            @if (filled($evento->tarjeta->descripcion))
                <p class="evento-ficha-descripcion">{{ $evento->tarjeta->descripcion }}</p>
            @endif

            <dl class="evento-ficha-datos">
                <div>
                    <dt>Fecha</dt>
                    <dd>{{ $evento->tarjeta->fechaFormateada() }}</dd>
                </div>
                <div>
                    <dt>Hora</dt>
                    <dd>{{ $evento->tarjeta->horaFormateada() }}</dd>
                </div>
                <div>
                    <dt>Estado</dt>
                    <dd>{{ $evento->estadoEtiqueta }}</dd>
                </div>
            </dl>

            <a class="evento-tarjeta-accion" href="{{ route('eventos', $filtro->query()) }}">
                Cerrar
            </a>
        </div>
    </article>
</dialog>
