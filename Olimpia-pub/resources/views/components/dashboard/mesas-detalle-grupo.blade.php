@props([
    'grupo',
])

<section class="mesas-detalle" aria-label="Detalle del grupo">
    <p><strong>Tipo:</strong> Grupo</p>
    <p><strong>Mesas:</strong> {{ $grupo->etiqueta() }}</p>
    <p><strong>Estado:</strong> {{ $grupo->ocupada ? 'Con pedido activo' : 'Libre' }}</p>
    <p><strong>Cuenta:</strong> {{ $grupo->cuentaFormateada() }}</p>

    <ul class="mesas-detalle-miembros">
        @foreach ($grupo->mesas as $mesa)
            <li>
                <span>{{ $mesa->etiqueta() }}</span>
                <span>{{ $mesa->codigo }}</span>
                @if ($mesa->qrSvg !== '')
                    <figure class="mesas-detalle-qr">
                        <div class="mesas-detalle-qr-marca" aria-hidden="true">
                            {!! $mesa->qrSvg !!}
                        </div>
                    </figure>
                @endif
            </li>
        @endforeach
    </ul>
</section>
