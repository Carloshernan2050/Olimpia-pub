@props([
    'resumen',
])

<ul class="inventario-resumen" aria-label="Resumen de inventario">
    <li>
        <article class="inventario-resumen-tarjeta">
            <x-dashboard.icono nombre="caja" />
            <div>
                <p>{{ $resumen->productos }}</p>
                <span>Productos</span>
            </div>
        </article>
    </li>
    <li>
        <article class="inventario-resumen-tarjeta inventario-resumen-tarjeta-azul">
            <x-dashboard.icono nombre="portapapeles" />
            <div>
                <p>{{ $resumen->movimientos }}</p>
                <span>Movimientos</span>
            </div>
        </article>
    </li>
    <li>
        <article class="inventario-resumen-tarjeta inventario-resumen-tarjeta-amarilla">
            <x-dashboard.icono nombre="tendencia-baja" />
            <div>
                <p>{{ $resumen->stockBajo }}</p>
                <span>Stock bajo</span>
            </div>
        </article>
    </li>
    <li>
        <article class="inventario-resumen-tarjeta inventario-resumen-tarjeta-roja">
            <x-dashboard.icono nombre="carrito-alerta" />
            <div>
                <p>{{ $resumen->agotados }}</p>
                <span>Agotados</span>
            </div>
        </article>
    </li>
</ul>
