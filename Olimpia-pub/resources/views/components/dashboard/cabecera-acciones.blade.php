@props([
    'accionesCabecera',
    'nombreUsuario',
])

<div class="dashboard-acciones">
    @foreach ($accionesCabecera as $accion)
        @if ($accion->esPerfil)
            <details class="menu-perfil" data-menu-perfil data-cerrar-al-pulsar-fuera>
                <summary aria-label="{{ $accion->etiqueta }}">
                    <x-dashboard.icono :nombre="$accion->icono" />
                </summary>
                <div class="menu-perfil-panel">
                    <p>Hola, {{ $nombreUsuario }}</p>
                    <x-dashboard.cerrar-sesion />
                </div>
            </details>
        @else
            <span
                class="dashboard-accion"
                title="Próximamente"
                aria-label="{{ $accion->etiqueta }} (próximamente)"
            >
                <x-dashboard.icono :nombre="$accion->icono" />
            </span>
        @endif
    @endforeach
</div>
