@props([
    'mesa',
    'filtro',
])

<tr class="mesas-fila">
    <th scope="row">
        <div class="mesas-fila-mesa">
            <strong>{{ $mesa->etiqueta }}</strong>
            <span>{{ $mesa->subtitulo }}</span>
        </div>
    </th>
    <td>
        @if ($mesa->ocupada)
            <a
                class="mesas-pedido-resumen"
                href="{{ route('mesas', $mesa->queryPedido($filtro->query())) }}"
                aria-label="Ver pedido de {{ $mesa->etiqueta }}"
            >
                {{ $mesa->resumenPedido() }}
            </a>
        @else
            <span class="mesas-pedido-vacio">{{ $mesa->resumenPedido() }}</span>
        @endif
    </td>
    <td class="mesas-fila-cuenta">{{ $mesa->cuentaFormateada() }}</td>
    <td>
        <div class="mesas-fila-acciones">
            @if ($mesa->ocupada)
                <form
                    method="POST"
                    action="{{ $mesa->rutaTerminarPedido() }}"
                    onsubmit="return confirm({{ \Illuminate\Support\Js::from($mesa->mensajeTerminar()) }})"
                >
                    @csrf
                    <button
                        class="mesas-accion mesas-accion-terminar"
                        type="submit"
                        aria-label="Terminar pedido de {{ $mesa->etiqueta }}"
                    >
                        <x-dashboard.icono nombre="check" />
                    </button>
                </form>
            @endif
            <a
                class="mesas-accion mesas-accion-ver"
                href="{{ route('mesas', $mesa->queryVer($filtro->query())) }}"
                aria-label="Ver {{ $mesa->etiqueta }}"
            >
                <x-dashboard.icono nombre="ojo" />
            </a>
            <a
                class="mesas-accion mesas-accion-editar"
                href="{{ route('mesas', $mesa->queryEditar($filtro->query())) }}"
                aria-label="Editar {{ $mesa->etiqueta }}"
            >
                <x-dashboard.icono nombre="lapiz" />
            </a>
            <form
                method="POST"
                action="{{ $mesa->rutaEliminar() }}"
                onsubmit="return confirm({{ \Illuminate\Support\Js::from(
                    $mesa->mensajeEliminar()
                ) }})"
            >
                @csrf
                @method('DELETE')
                <button
                    class="mesas-accion mesas-accion-eliminar"
                    type="submit"
                    aria-label="{{ $mesa->esGrupo ? 'Separar '.$mesa->etiqueta : 'Eliminar '.$mesa->etiqueta }}"
                >
                    <x-dashboard.icono nombre="papelera" />
                </button>
            </form>
        </div>
    </td>
</tr>
