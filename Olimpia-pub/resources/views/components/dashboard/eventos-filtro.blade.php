@props([
    'filtro',
])

<details
    class="eventos-filtro-menu @if ($filtro->estaActivo()) is-activo @endif"
    data-cerrar-al-pulsar-fuera
>
    <summary class="eventos-filtro" aria-label="Filtrar eventos">
        <span>Filtrar</span>
        <x-dashboard.icono nombre="filtro" />
    </summary>

    <form class="eventos-filtro-panel" method="GET" action="{{ route('eventos') }}">
        <p class="eventos-filtro-titulo">Fechas</p>

        <div class="eventos-filtro-fechas">
            <label>
                <span>Desde</span>
                <input type="date" name="desde" value="{{ $filtro->desde }}">
            </label>
            <label>
                <span>Hasta</span>
                <input type="date" name="hasta" value="{{ $filtro->hasta }}">
            </label>
        </div>

        <div class="eventos-filtro-acciones">
            <button class="eventos-filtro-aplicar" type="submit">Aplicar</button>
            <a class="eventos-filtro-limpiar" href="{{ route('eventos') }}">Limpiar</a>
        </div>
    </form>
</details>
