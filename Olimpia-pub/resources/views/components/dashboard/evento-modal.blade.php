@props([
    'filtro',
    'eventoEditar',
    'eventosGestion',
    'abrir' => false,
])

<dialog
    class="modal-evento"
    data-modal-evento
    aria-labelledby="titulo-modal-evento"
    @if ($abrir) data-abrir @endif
>
    <div class="modal-evento-caja">
        <header class="modal-evento-cabecera">
            <h2 id="titulo-modal-evento">
                {{ $eventoEditar ? 'Editar evento' : 'Agregar evento' }}
            </h2>
            <button type="button" class="modal-evento-cerrar" data-cerrar-modal-evento aria-label="Cerrar">
                <x-dashboard.icono nombre="cerrar" />
            </button>
        </header>

        <x-dashboard.evento-formulario :evento-editar="$eventoEditar" />

        @if (count($eventosGestion) > 0)
            <section class="modal-evento-listado" aria-label="Eventos guardados">
                <h3>Eventos guardados</h3>
                <ul>
                    @foreach ($eventosGestion as $item)
                        <li>
                            <div>
                                <p>{{ $item->nombre }}</p>
                                <span>{{ $item->cuando() }}</span>
                            </div>
                            <div class="modal-evento-item-acciones">
                                <a
                                    href="{{ route('eventos', [...$filtro->query(), 'editar' => $item->id]) }}"
                                    aria-label="Editar {{ $item->nombre }}"
                                >
                                    <x-dashboard.icono nombre="lapiz" />
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('eventos.eliminar', $item->id) }}"
                                    onsubmit="return confirm({{ \Illuminate\Support\Js::from(
                                        '¿Eliminar el evento '.$item->nombre.'?'
                                    ) }})"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Eliminar {{ $item->nombre }}">
                                        <x-dashboard.icono nombre="papelera" />
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</dialog>
