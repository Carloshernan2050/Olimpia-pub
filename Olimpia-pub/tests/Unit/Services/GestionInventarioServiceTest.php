<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\MovimientoInventarioGestionDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoNombreDuplicadoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\GestionInventarioService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionInventarioServiceTest extends TestCase
{
    private ProductoRepositoryInterface&MockInterface $productos;

    private MovimientoInventarioRepositoryInterface&MockInterface $movimientos;

    private GestionInventarioService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productos = Mockery::mock(ProductoRepositoryInterface::class);
        $this->movimientos = Mockery::mock(MovimientoInventarioRepositoryInterface::class);
        $this->service = new GestionInventarioService($this->productos, $this->movimientos);
    }

    public function test_crear_entrada_aumenta_el_stock(): void
    {
        $datos = $this->datos();
        $producto = $this->producto(['stock' => 10]);
        $movimiento = $this->movimiento();

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);
        $this->productos->shouldReceive('update')
            ->once()
            ->with($producto, ['stock' => 15])
            ->andReturn($producto);
        $this->movimientos->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => $payload['id_producto'] === 7 && $payload['cantidad'] === 5))
            ->andReturn($movimiento);

        $creado = $this->service->crear($datos, 4);

        $this->assertInstanceOf(MovimientoInventarioGestionDatos::class, $creado);
        $this->assertSame(9, $creado->id);
        $this->assertSame('Limonada', $creado->nombreProducto);
    }

    public function test_salida_falla_si_no_hay_stock(): void
    {
        $datos = GuardarMovimientoInventarioDatos::fromValidated([
            'id_producto' => 7,
            'tipo_movimiento' => 'salida',
            'cantidad' => 8,
        ]);
        $producto = $this->producto(['stock' => 3]);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);

        $this->expectException(StockInsuficienteException::class);

        $this->service->crear($datos, 4);
    }

    public function test_actualizar_falla_si_no_existe_el_movimiento(): void
    {
        $this->movimientos->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->expectException(MovimientoInventarioNoEncontradoException::class);

        $this->service->actualizar(99, $this->datos());
    }

    public function test_eliminar_revierte_el_stock(): void
    {
        $producto = $this->producto(['stock' => 15]);
        $movimiento = $this->movimiento();

        $this->movimientos->shouldReceive('findById')->once()->with(9)->andReturn($movimiento);
        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);
        $this->productos->shouldReceive('update')
            ->once()
            ->with($producto, ['stock' => 10])
            ->andReturn($producto);
        $this->movimientos->shouldReceive('delete')->once()->with($movimiento);

        $this->service->eliminar(9);
    }

    public function test_eliminar_producto_falla_si_tiene_pedidos(): void
    {
        $detalles = Mockery::mock(HasMany::class);
        $detalles->shouldReceive('exists')->once()->andReturn(true);
        $producto = Mockery::mock(Producto::class)->makePartial();
        $producto->shouldReceive('detallesPedido')->once()->andReturn($detalles);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);

        $this->expectException(ProductoConPedidosException::class);

        $this->service->eliminarProducto(7);
    }

    public function test_listar_mapea_los_movimientos_recientes(): void
    {
        $this->movimientos->shouldReceive('recientes')
            ->once()
            ->andReturn(collect([$this->movimiento()]));

        $listado = $this->service->listar();

        $this->assertCount(1, $listado);
        $this->assertSame('Limonada', $listado[0]->nombreProducto);
    }

    public function test_crear_producto_con_stock_registra_entrada_inicial(): void
    {
        $datos = $this->datosProducto();
        $producto = $this->productoConCategoria(['stock' => 12]);

        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn(null);
        $this->productos->shouldReceive('create')
            ->once()
            ->with($datos->paraCrear())
            ->andReturn($producto);
        $this->movimientos->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => $payload['tipo_movimiento'] === 'entrada'
                && $payload['cantidad'] === 12
                && $payload['id_producto'] === 7
                && $payload['id_usuario'] === 4))
            ->andReturn($this->movimiento());

        $creado = $this->service->crearProducto($datos, 4);

        $this->assertInstanceOf(ProductoInventarioDatos::class, $creado);
        $this->assertSame('Limonada', $creado->nombre);
        $this->assertSame(12, $creado->stock);
        $this->assertSame('Bebidas', $creado->categoria);
    }

    public function test_crear_producto_sin_stock_no_registra_movimiento(): void
    {
        $datos = $this->datosProducto(['stock' => 0]);
        $producto = $this->productoConCategoria(['stock' => 0]);

        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn(null);
        $this->productos->shouldReceive('create')->once()->with($datos->paraCrear())->andReturn($producto);
        $this->movimientos->shouldReceive('create')->never();

        $creado = $this->service->crearProducto($datos, 4);

        $this->assertSame(0, $creado->stock);
    }

    public function test_crear_producto_falla_si_el_nombre_ya_existe(): void
    {
        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn($this->producto());
        $this->productos->shouldReceive('create')->never();

        $this->expectException(ProductoNombreDuplicadoException::class);

        $this->service->crearProducto($this->datosProducto(), 4);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function datos(array $extra = []): GuardarMovimientoInventarioDatos
    {
        return GuardarMovimientoInventarioDatos::fromValidated([
            'id_producto' => 7,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 5,
            ...$extra,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function datosProducto(array $extra = []): GuardarProductoInventarioDatos
    {
        return GuardarProductoInventarioDatos::fromValidated([
            'nombre' => 'Limonada',
            'descripcion' => 'Natural',
            'precio' => '8.50',
            'stock' => 12,
            'id_categoria' => 2,
            'estado' => 'activo',
            ...$extra,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function productoConCategoria(array $extra = []): Producto
    {
        $producto = $this->producto($extra);
        $producto->setRelation('categoria', new Categoria(['nombre' => 'Bebidas']));

        return $producto;
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function producto(array $extra = []): Producto
    {
        $producto = new Producto([
            'nombre' => 'Limonada',
            'precio' => '8.50',
            'stock' => 10,
            'estado' => 'activo',
            ...$extra,
        ]);
        $producto->id_producto = 7;

        return $producto;
    }

    private function movimiento(): MovimientoInventario
    {
        $movimiento = new MovimientoInventario([
            'tipo_movimiento' => 'entrada',
            'cantidad' => 5,
            'fecha' => '2026-08-01 12:00:00',
            'id_producto' => 7,
            'id_usuario' => 4,
        ]);
        $movimiento->id_movimiento = 9;
        $movimiento->setRelation('producto', $this->producto());

        return $movimiento;
    }
}
