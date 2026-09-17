<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\DetallePedidoRepositoryInterface;
use App\Contracts\Repositories\HistorialRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\Enums\AccionHistorial;
use App\Enums\EstadoPedido;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\GestionPedidosService;
use Illuminate\Database\ConnectionInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionPedidosServiceTest extends TestCase
{
    private MesaRepositoryInterface&MockInterface $mesas;

    private PedidoRepositoryInterface&MockInterface $pedidos;

    private DetallePedidoRepositoryInterface&MockInterface $detalles;

    private ProductoRepositoryInterface&MockInterface $productos;

    private HistorialRepositoryInterface&MockInterface $historial;

    private ConnectionInterface&MockInterface $conexion;

    private GestionPedidosService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mesas = Mockery::mock(MesaRepositoryInterface::class);
        $this->pedidos = Mockery::mock(PedidoRepositoryInterface::class);
        $this->detalles = Mockery::mock(DetallePedidoRepositoryInterface::class);
        $this->productos = Mockery::mock(ProductoRepositoryInterface::class);
        $this->historial = Mockery::mock(HistorialRepositoryInterface::class);
        $this->conexion = Mockery::mock(ConnectionInterface::class);
        $this->conexion->shouldReceive('transaction')->andReturnUsing(fn (callable $accion) => $accion());
        $this->service = new GestionPedidosService(
            $this->mesas,
            $this->pedidos,
            $this->detalles,
            $this->productos,
            $this->historial,
            $this->conexion,
        );
    }

    public function test_falla_si_la_mesa_no_existe(): void
    {
        $this->mesas->shouldReceive('findById')->once()->with(9)->andReturn(null);

        $this->expectException(MesaNoEncontradaException::class);

        $this->service->crearActivo(new GuardarPedidoMesaDatos(9, []));
    }

    public function test_falla_si_ya_hay_un_pedido_activo(): void
    {
        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn(new Pedido);

        $this->expectException(PedidoActivoYaExisteException::class);

        $this->service->crearActivo(new GuardarPedidoMesaDatos(3, [
            ['id_producto' => 1, 'cantidad' => 1],
        ]));
    }

    public function test_falla_si_el_producto_no_existe(): void
    {
        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn(null);
        $this->productos->shouldReceive('findById')->once()->with(88)->andReturn(null);

        $this->expectException(ProductoInventarioNoEncontradoException::class);

        $this->service->crearActivo(new GuardarPedidoMesaDatos(3, [
            ['id_producto' => 88, 'cantidad' => 1],
        ]));
    }

    public function test_crea_el_pedido_activo_con_sus_productos(): void
    {
        $producto = new Producto(['nombre' => 'Limonada', 'precio' => '8.50']);
        $producto->id_producto = 7;
        $pedido = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '17.00',
            'id_mesa' => 3,
        ]);
        $pedido->id_pedido = 21;
        $detalle = new DetallePedido([
            'cantidad' => 2,
            'precio_unitario' => '8.50',
            'subtotal' => '17.00',
        ]);
        $detalle->setRelation('producto', $producto);
        $pedido->setRelation('detalles', collect([$detalle]));

        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn(null);
        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);
        $this->pedidos->shouldReceive('create')->once()->andReturn($pedido);
        $this->detalles->shouldReceive('create')->once()->with(Mockery::on(function (array $datos): bool {
            return $datos['id_pedido'] === 21
                && $datos['id_producto'] === 7
                && $datos['cantidad'] === 2
                && $datos['subtotal'] === '17.00';
        }))->andReturn($detalle);
        $this->pedidos->shouldReceive('findById')->once()->with(21)->andReturn($pedido);

        $creado = $this->service->crearActivo(new GuardarPedidoMesaDatos(3, [
            ['id_producto' => 7, 'cantidad' => 2],
        ]));

        $this->assertSame(21, $creado->id);
        $this->assertSame('$ 17,00', $creado->totalFormateado());
        $this->assertSame('Limonada', $creado->enOrden()[0]->nombre);
    }

    public function test_registrar_crea_el_pedido_si_la_mesa_esta_libre(): void
    {
        $producto = new Producto(['nombre' => 'Limonada', 'precio' => '8.50']);
        $producto->id_producto = 7;
        $pedido = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '8.50',
            'id_mesa' => 3,
        ]);
        $pedido->id_pedido = 21;
        $detalle = new DetallePedido([
            'cantidad' => 1,
            'precio_unitario' => '8.50',
            'subtotal' => '8.50',
        ]);
        $detalle->setRelation('producto', $producto);
        $pedido->setRelation('detalles', collect([$detalle]));

        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn(null);
        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);
        $this->pedidos->shouldReceive('create')->once()->andReturn($pedido);
        $this->detalles->shouldReceive('create')->once()->andReturn($detalle);
        $this->pedidos->shouldReceive('findById')->once()->with(21)->andReturn($pedido);

        $creado = $this->service->registrar(new GuardarPedidoMesaDatos(3, [
            ['id_producto' => 7, 'cantidad' => 1],
        ]));

        $this->assertSame(21, $creado->id);
        $this->assertSame('$ 8,50', $creado->totalFormateado());
    }

    public function test_registrar_suma_productos_al_pedido_activo(): void
    {
        $producto = new Producto(['nombre' => 'Agua', 'precio' => '2.00']);
        $producto->id_producto = 4;
        $activo = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '8.50',
            'id_mesa' => 3,
        ]);
        $activo->id_pedido = 21;
        $actualizado = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '12.50',
            'id_mesa' => 3,
        ]);
        $actualizado->id_pedido = 21;
        $detalle = new DetallePedido([
            'cantidad' => 2,
            'precio_unitario' => '2.00',
            'subtotal' => '4.00',
        ]);
        $detalle->setRelation('producto', $producto);
        $actualizado->setRelation('detalles', collect([$detalle]));

        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn($activo);
        $this->productos->shouldReceive('findById')->once()->with(4)->andReturn($producto);
        $this->detalles->shouldReceive('create')->once()->with(Mockery::on(function (array $datos): bool {
            return $datos['id_pedido'] === 21
                && $datos['id_producto'] === 4
                && $datos['cantidad'] === 2
                && $datos['subtotal'] === '4.00';
        }))->andReturn($detalle);
        $this->pedidos->shouldReceive('update')->once()->with($activo, ['total' => '12.50'])->andReturn($activo);
        $this->pedidos->shouldReceive('findById')->once()->with(21)->andReturn($actualizado);

        $pedido = $this->service->registrar(new GuardarPedidoMesaDatos(3, [
            ['id_producto' => 4, 'cantidad' => 2],
        ]));

        $this->assertSame('$ 12,50', $pedido->totalFormateado());
        $this->assertSame('Agua', $pedido->enOrden()[0]->nombre);
    }

    public function test_terminar_falla_si_no_hay_pedido_activo(): void
    {
        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn(new Mesa(['numero_mesa' => 3]));
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn(null);

        $this->expectException(PedidoActivoNoEncontradoException::class);

        $this->service->terminar(3, 4);
    }

    public function test_terminar_cierra_el_pedido_y_lo_registra_en_historial(): void
    {
        $mesa = new Mesa(['numero_mesa' => 3]);
        $mesa->id_mesa = 3;
        $pedido = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '17.00',
            'id_mesa' => 3,
        ]);
        $pedido->id_pedido = 21;
        $pedido->setRelation('detalles', collect());

        $this->mesas->shouldReceive('findById')->once()->with(3)->andReturn($mesa);
        $this->pedidos->shouldReceive('activoDeMesa')->once()->with(3)->andReturn($pedido);
        $this->pedidos->shouldReceive('update')->once()->with($pedido, [
            'estado' => EstadoPedido::Cerrado->value,
        ])->andReturn($pedido);
        $this->historial->shouldReceive('create')->once()->with(Mockery::on(function (array $datos): bool {
            return $datos['accion'] === AccionHistorial::TerminarPedido->value
                && $datos['id_usuario'] === 4
                && $datos['descripcion'] === 'Pedido de Mesa 3 cerrado. Total $ 17,00.';
        }));

        $this->service->terminar(3, 4);
    }
}
