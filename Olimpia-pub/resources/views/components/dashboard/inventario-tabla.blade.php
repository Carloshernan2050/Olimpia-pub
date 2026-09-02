@props([
    'productos',
    'filtro',
])

<div class="inventario-tabla-envoltorio">
    <table class="inventario-tabla">
        <thead>
            <tr>
                <th scope="col">Producto</th>
                <th scope="col">Detalle</th>
                <th scope="col">Categoría</th>
                <th scope="col">Precio</th>
                <th scope="col">Stock</th>
                <th scope="col">Estado</th>
                <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <x-dashboard.inventario-fila :producto="$producto" :filtro="$filtro" />
            @endforeach
        </tbody>
    </table>
</div>
