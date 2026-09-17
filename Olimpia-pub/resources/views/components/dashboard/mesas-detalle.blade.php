@props([
    'mesa',
])

<section class="mesas-detalle" aria-label="Detalle de la mesa">
    <p><strong>Tipo:</strong> {{ $mesa->tipo->etiqueta() }}</p>
    <p><strong>Código:</strong> {{ $mesa->codigo }}</p>
    <p><strong>Estado:</strong> {{ $mesa->ocupada ? 'Con pedido activo' : 'Libre' }}</p>
    <p><strong>Cuenta:</strong> {{ $mesa->cuentaFormateada() }}</p>

    <figure class="mesas-detalle-qr">
        <div class="mesas-detalle-qr-marca" aria-hidden="true">
            {!! $mesa->qrSvg !!}
        </div>
        <figcaption>{{ $mesa->codigo }}</figcaption>
    </figure>
</section>
