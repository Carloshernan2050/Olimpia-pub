@props([
    'mesa',
])

<section class="mesas-pedido-detalle" aria-label="Pedido activo">
    @if ($mesa->pedidoActivo === null || ! $mesa->pedidoActivo->tieneLineas())
        <p class="mesas-pedido-detalle-vacio">Sin pedido activo.</p>
    @else
        <ul class="mesas-pedido-detalle-lineas">
            @foreach ($mesa->pedidoActivo->enOrden() as $linea)
                <li class="mesas-pedido-detalle-linea">
                    @if ($linea->tieneImagen())
                        <img
                            class="mesas-pedido-detalle-foto"
                            src="{{ $linea->urlImagenPublica() }}"
                            alt=""
                        >
                    @else
                        <span class="mesas-pedido-detalle-foto is-vacia" aria-hidden="true">
                            <x-dashboard.icono nombre="imagen" />
                        </span>
                    @endif
                    <div>
                        <p>{{ $linea->nombre }}</p>
                        <span>{{ $linea->cantidad }} × {{ $linea->precioFormateado() }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
        <p class="mesas-pedido-detalle-total">
            <strong>Cuenta:</strong> {{ $mesa->pedidoActivo->totalFormateado() }}
        </p>
        <form
            class="mesas-terminar-formulario"
            method="POST"
            action="{{ $mesa->rutaTerminarPedido() }}"
            onsubmit="return confirm({{ \Illuminate\Support\Js::from($mesa->mensajeTerminar()) }})"
        >
            @csrf
            <button class="mesas-terminar" type="submit">
                Terminar pedido
            </button>
        </form>
    @endif
</section>
