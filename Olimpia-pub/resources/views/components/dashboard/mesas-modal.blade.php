@props([
    'filtro',
    'tipos',
    'mesasUnibles' => [],
    'mesaVer' => null,
    'mesaEditar' => null,
    'pedidoVer' => null,
    'grupoVer' => null,
    'grupoEditar' => null,
    'pedidoGrupoVer' => null,
    'unir' => false,
    'liberar' => false,
    'grupos' => [],
    'siguienteNumero' => 1,
    'abrir' => false,
])

@php
    $tituloModal = 'Añadir mesa';

    if ($pedidoGrupoVer) {
        $tituloModal = 'Pedido de '.$pedidoGrupoVer->etiqueta();
    } elseif ($pedidoVer) {
        $tituloModal = 'Pedido de '.$pedidoVer->etiqueta();
    } elseif ($grupoVer) {
        $tituloModal = $grupoVer->etiqueta();
    } elseif ($mesaVer) {
        $tituloModal = $mesaVer->etiqueta();
    } elseif ($grupoEditar) {
        $tituloModal = 'Editar '.$grupoEditar->etiqueta();
    } elseif ($unir) {
        $tituloModal = 'Unir mesas';
    } elseif ($liberar) {
        $tituloModal = 'Liberar mesas';
    } elseif ($mesaEditar) {
        $tituloModal = 'Editar '.$mesaEditar->etiqueta();
    }
@endphp

<dialog
    class="modal-mesas"
    data-modal-mesa
    aria-labelledby="titulo-modal-mesas"
    @if ($abrir) data-abrir @endif
>
    <div class="modal-mesas-caja">
        <header class="modal-mesas-cabecera">
            <h2 id="titulo-modal-mesas">{{ $tituloModal }}</h2>
            <button type="button" class="modal-mesas-cerrar" data-cerrar-modal-mesa aria-label="Cerrar">
                <x-dashboard.icono nombre="cerrar" />
            </button>
        </header>

        @if ($pedidoGrupoVer)
            <x-dashboard.mesas-pedido-detalle :mesa="$pedidoGrupoVer" />
        @elseif ($pedidoVer)
            <x-dashboard.mesas-pedido-detalle :mesa="$pedidoVer" />
        @elseif ($grupoVer)
            <x-dashboard.mesas-detalle-grupo :grupo="$grupoVer" />
        @elseif ($mesaVer)
            <x-dashboard.mesas-detalle :mesa="$mesaVer" />
        @elseif ($unir || $grupoEditar)
            <x-dashboard.mesas-formulario-grupo
                :grupo-editar="$grupoEditar"
                :mesas-unibles="$mesasUnibles"
            />
        @elseif ($liberar)
            <x-dashboard.mesas-formulario-liberar :grupos="$grupos" />
        @else
            <x-dashboard.mesas-formulario
                :mesa-editar="$mesaEditar"
                :tipos="$tipos"
                :siguiente-numero="$siguienteNumero"
            />
        @endif
    </div>
</dialog>
