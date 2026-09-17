@props([
    'filtro',
    'categorias',
])

<header class="menu-encabezado">
    <h1 class="menu-titulo" id="titulo-menu">
        <span>Menú</span>
    </h1>

    <x-dashboard.menu-filtro :filtro="$filtro" :categorias="$categorias" />
</header>
