@extends('layouts.dashboard')

@section('titulo', 'Mesas — '.config('app.name', 'Olimpia'))

@section('contenido')
    <section class="catalogo-mesas" aria-labelledby="titulo-mesas">
        <header class="mesas-encabezado">
            <h1 class="mesas-titulo" id="titulo-mesas">
                <span>Mesas</span>
            </h1>
            @if ($catalogo->puedeUnir() || $catalogo->puedeLiberar())
                <div class="mesas-acciones-catalogo">
                    @if ($catalogo->puedeUnir())
                        <a
                            class="mesas-unir"
                            href="{{ route('mesas', [...$filtro->query(), 'unir' => 1]) }}"
                        >
                            Unir mesas
                        </a>
                    @endif
                    @if ($catalogo->puedeLiberar())
                        <a
                            class="mesas-unir"
                            href="{{ route('mesas', [...$filtro->query(), 'liberar' => 1]) }}"
                        >
                            Liberar mesas
                        </a>
                    @endif
                </div>
            @endif
        </header>

        <x-dashboard.mesas-tipos :tipos="$catalogo->tipos" :filtro="$filtro" />

        <div class="mesas-panel">
            @if ($catalogo->tieneMesas())
                <x-dashboard.mesas-lista :mesas="$catalogo->enOrden()" :filtro="$filtro" />
            @elseif ($filtro->tipo?->esGrupo())
                <p class="mesas-vacio">No hay mesas unidas.</p>
            @elseif ($filtro->tipo?->esPedidosActivos())
                <p class="mesas-vacio">No hay pedidos activos.</p>
            @else
                <p class="mesas-vacio">No hay mesas en el catálogo.</p>
            @endif
        </div>
    </section>

    <x-dashboard.boton-agregar-mesa
        :filtro="$filtro"
        :edicion="$mesaVer !== null || $mesaEditar !== null || $pedidoVer !== null || $grupoVer !== null || $grupoEditar !== null || $pedidoGrupoVer !== null || $unir || $liberar"
    />

    <x-dashboard.mesas-modal
        :filtro="$filtro"
        :tipos="$catalogo->tiposPersistibles"
        :mesas-unibles="$catalogo->opcionesUnir($grupoEditar)"
        :mesa-ver="$mesaVer"
        :mesa-editar="$mesaEditar"
        :pedido-ver="$pedidoVer"
        :grupo-ver="$grupoVer"
        :grupo-editar="$grupoEditar"
        :pedido-grupo-ver="$pedidoGrupoVer"
        :unir="$unir"
        :liberar="$liberar"
        :grupos="$catalogo->grupos"
        :siguiente-numero="$catalogo->siguienteNumero"
        :abrir="$abrirModal || ($errors->any() && in_array(old('formulario'), ['mesa', 'grupo', 'liberar'], true))"
    />
@endsection
