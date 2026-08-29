@props([
    'filtro',
    'promocionEditar',
    'promocionesGestion',
    'abrir' => false,
])

<dialog
    class="modal-promocion"
    data-modal-promocion
    aria-labelledby="titulo-modal-promocion"
    @if ($abrir) data-abrir @endif
>
    <div class="modal-promocion-caja">
        <header class="modal-promocion-cabecera">
            <h2 id="titulo-modal-promocion">
                {{ $promocionEditar ? 'Editar promoción' : 'Agregar promoción' }}
            </h2>
            <button type="button" class="modal-promocion-cerrar" data-cerrar-modal-promocion aria-label="Cerrar">
                <x-dashboard.icono nombre="cerrar" />
            </button>
        </header>

        <x-dashboard.promocion-formulario :promocion-editar="$promocionEditar" />

        @if (count($promocionesGestion) > 0)
            <section class="modal-promocion-listado" aria-label="Promociones guardadas">
                <h3>Promociones guardadas</h3>
                <ul>
                    @foreach ($promocionesGestion as $item)
                        <li>
                            <div>
                                <p>{{ $item->nombre }}</p>
                                <span>{{ $item->fechaInicio }} — {{ $item->fechaFin }}</span>
                            </div>
                            <div class="modal-promocion-item-acciones">
                                <a
                                    href="{{ route('promociones', [...$filtro->query(), 'editar' => $item->id]) }}"
                                    aria-label="Editar {{ $item->nombre }}"
                                >
                                    <x-dashboard.icono nombre="lapiz" />
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('promociones.eliminar', $item->id) }}"
                                    onsubmit="return confirm({{ \Illuminate\Support\Js::from(
                                        '¿Eliminar la promoción '.$item->nombre.'?'
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
