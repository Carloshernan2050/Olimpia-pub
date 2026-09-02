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
