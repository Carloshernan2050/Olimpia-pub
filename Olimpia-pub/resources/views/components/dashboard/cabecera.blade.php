@props([
    'accionesCabecera',
    'nombreUsuario',
])

<header class="dashboard-cabecera">
    <div class="dashboard-cabecera-interior dashboard-interior">
        <a class="dashboard-marca" href="{{ route('dashboard') }}" aria-label="Olimpia, ir a inicio">
            <span class="dashboard-marca-nombre">Olimpia</span>
            <x-dashboard.icono nombre="balon" />
        </a>

        <div class="dashboard-buscador">
            <form method="GET" action="{{ route('dashboard') }}" role="search">
                <label class="sr-only" for="busqueda-dashboard">Buscar</label>
                <div class="dashboard-buscador-caja">
                    <x-dashboard.icono nombre="buscar" />
                    <input
                        id="busqueda-dashboard"
                        name="q"
                        type="search"
                        placeholder="Search"
                        value="{{ request('q') }}"
                        autocomplete="off"
                    >
                </div>
            </form>
        </div>

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
                        class="dashboard-accion is-inactivo"
                        title="Próximamente"
                        aria-label="{{ $accion->etiqueta }} (próximamente)"
                    >
                        <x-dashboard.icono :nombre="$accion->icono" />
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</header>
