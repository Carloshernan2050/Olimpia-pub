@props(['codigo'])

<aside class="pedido-barra" data-pedido-barra hidden>
    <form
        class="pedido-barra-formulario"
        method="POST"
        action="{{ route('mesa.pedir', ['codigo' => $codigo]) }}"
        data-pedido-formulario
    >
        @csrf
        <div class="pedido-barra-campos" data-pedido-campos></div>
        @error('lineas')
            <p class="pedido-barra-error" role="alert">{{ $message }}</p>
        @enderror

        <div class="pedido-barra-resumen">
            <p class="pedido-barra-detalle">
                <span data-pedido-cantidad>0</span>
                <span data-pedido-plural>productos</span>
            </p>
            <p class="pedido-barra-total" data-pedido-total>$ 0,00</p>
        </div>

        <ul class="pedido-barra-lista" data-pedido-lista aria-label="Pedido"></ul>

        <button class="menu-realizar-pedido pedido-enviar" type="submit">
            <x-dashboard.icono nombre="carrito" />
            <span>Enviar pedido</span>
        </button>
    </form>
</aside>
