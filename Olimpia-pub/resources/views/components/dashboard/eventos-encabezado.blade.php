@props([
    'filtro',
])

<header class="eventos-encabezado">
    <h1 class="eventos-titulo" id="titulo-eventos">
        <span>Eventos</span>
    </h1>

    <x-dashboard.eventos-filtro :filtro="$filtro" />
</header>
