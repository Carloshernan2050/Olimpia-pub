@props([
    'mesas',
    'filtro',
])

<div class="mesas-tabla-envoltorio">
    <table class="mesas-tabla">
        <caption class="sr-only">Mesas del local</caption>
        <thead>
            <tr>
                <th scope="col">Mesa</th>
                <th scope="col">Pedido</th>
                <th scope="col">Cuenta</th>
                <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mesas as $mesa)
                <x-dashboard.mesas-fila :mesa="$mesa" :filtro="$filtro" />
            @endforeach
        </tbody>
    </table>
</div>
