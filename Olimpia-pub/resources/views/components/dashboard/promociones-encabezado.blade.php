@props([
    'filtro',
])

<header class="promociones-encabezado">
    <h1 class="promociones-titulo" id="titulo-promociones">
        <span>Promociones</span>
    </h1>

    <x-dashboard.promociones-filtro :filtro="$filtro" />
</header>
