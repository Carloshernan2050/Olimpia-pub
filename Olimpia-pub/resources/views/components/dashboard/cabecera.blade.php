@props([
    'accionesCabecera',
    'nombreUsuario',
])

<header class="dashboard-cabecera">
    <div class="dashboard-cabecera-interior">
        <x-dashboard.cabecera-marca />
        <x-dashboard.cabecera-buscador />
        <x-dashboard.cabecera-acciones
            :acciones-cabecera="$accionesCabecera"
            :nombre-usuario="$nombreUsuario"
        />
    </div>
</header>
