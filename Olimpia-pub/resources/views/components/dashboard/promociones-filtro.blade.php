@props([
    'filtro',
])

<details
    class="promociones-filtro-menu @if ($filtro->estaActivo()) is-activo @endif"
    data-cerrar-al-pulsar-fuera
>
    <summary class="promociones-filtro" aria-label="Filtrar promociones">
        <span>Filtrar</span>
        <x-dashboard.icono nombre="filtro" />
    </summary>

    <form class="promociones-filtro-panel" method="GET" action="{{ route('promociones') }}">
        <p class="promociones-filtro-titulo">Fechas</p>

        <div class="promociones-filtro-fechas">
            <label>
                <span>Desde</span>
                <input type="date" name="desde" value="{{ $filtro->desde }}">
            </label>
            <label>
                <span>Hasta</span>
                <input type="date" name="hasta" value="{{ $filtro->hasta }}">
            </label>
        </div>

        <div class="promociones-filtro-acciones">
            <button class="promociones-filtro-aplicar" type="submit">Aplicar</button>
            <a class="promociones-filtro-limpiar" href="{{ route('promociones') }}">Limpiar</a>
        </div>
    </form>
</details>
