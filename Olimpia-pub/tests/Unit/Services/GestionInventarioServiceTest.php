<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\MovimientoInventarioGestionDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\Exceptions\Inventario\MovimientoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoConPedidosException;
use App\Exceptions\Inventario\ProductoInventarioNoEncontradoException;
use App\Exceptions\Inventario\ProductoNombreDuplicadoException;
use App\Exceptions\Inventario\StockInsuficienteException;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\GestionInventarioService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionInventarioServiceTest extends TestCase
{
    private ProductoRepositoryInterface&MockInterface $productos;

    private MovimientoInventarioRepositoryInterface&MockInterface $movimientos;

    private AlmacenamientoImagenPublicaInterface&MockInterface $imagenes;

    private GestionInventarioService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productos = Mockery::mock(ProductoRepositoryInterface::class);
        $this->movimientos = Mockery::mock(MovimientoInventarioRepositoryInterface::class);
        $this->imagenes = Mockery::mock(AlmacenamientoImagenPublicaInterface::class);
        $this->service = new GestionInventarioService($this->productos, $this->movimientos, $this->imagenes);
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

    public function test_listar_de_producto_mapea_los_movimientos(): void
    {
        $this->movimientos->shouldReceive('porProducto')
            ->once()
            ->with(7)
            ->andReturn(collect([$this->movimiento()]));

        $listado = $this->service->listarDeProducto(7);

        $this->assertCount(1, $listado);
        $this->assertSame('Limonada', $listado[0]->nombreProducto);
    }

    public function test_buscar_devuelve_null_si_no_existe_el_movimiento(): void
    {
        $this->movimientos->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->assertNull($this->service->buscar(99));
    }

    public function test_buscar_devuelve_el_movimiento(): void
    {
        $this->movimientos->shouldReceive('findById')->once()->with(9)->andReturn($this->movimiento());

        $encontrado = $this->service->buscar(9);

        $this->assertSame(9, $encontrado?->id);
        $this->assertSame('Limonada', $encontrado?->nombreProducto);
    }

    public function test_buscar_producto_devuelve_null_si_no_existe(): void
    {
        $this->productos->shouldReceive('findById')->once()->with(99)->andReturn(null);

        $this->assertNull($this->service->buscarProducto(99));
    }

    public function test_crear_falla_si_el_producto_no_existe(): void
    {
        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn(null);

        $this->expectException(ProductoInventarioNoEncontradoException::class);

        $this->service->crear($this->datos(), 4);
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
        $this->assertSame(12, $creado->existencia->stock);
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

        $this->assertSame(0, $creado->existencia->stock);
    }

    public function test_crear_producto_guarda_la_imagen_si_se_envia(): void
    {
        $datos = $this->datosProducto();
        $archivo = UploadedFile::fake()->image('limonada.jpg');
        $producto = $this->productoConCategoria(['url_imagen' => 'productos/limonada.jpg']);

        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn(null);
        $this->imagenes->shouldReceive('guardar')->once()->with($archivo)->andReturn('productos/limonada.jpg');
        $this->productos->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => ($payload['url_imagen'] ?? null) === 'productos/limonada.jpg'))
            ->andReturn($producto);
        $this->movimientos->shouldReceive('create')->once()->andReturn($this->movimiento());

        $creado = $this->service->crearProducto($datos, 4, $archivo);

        $this->assertSame('productos/limonada.jpg', $creado->urlImagen);
        $this->assertTrue($creado->tieneImagen());
    }

    public function test_actualizar_producto_conserva_el_nombre_y_no_toca_el_stock(): void
    {
        $datos = $this->datosProducto(['precio' => '9.00', 'stock' => 10]);
        $actual = $this->productoConCategoria(['url_imagen' => 'productos/vieja.jpg']);
        $actualizado = $this->productoConCategoria(['precio' => '9.00', 'stock' => 10]);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($actual);
        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn($actual);
        $this->productos->shouldReceive('update')
            ->once()
            ->with($actual, Mockery::on(fn (array $payload): bool => $payload['precio'] === '9.00'
                && $payload['nombre'] === 'Limonada'
                && ! array_key_exists('stock', $payload)))
            ->andReturn($actualizado);
        $this->movimientos->shouldReceive('create')->never();

        $resultado = $this->service->actualizarProducto(7, $datos, 4);

        $this->assertSame('Limonada', $resultado->nombre);
        $this->assertSame(10, $resultado->existencia->stock);
    }

    public function test_actualizar_producto_ajusta_la_cantidad_con_un_movimiento(): void
    {
        $datos = $this->datosProducto(['stock' => 15]);
        $actual = $this->productoConCategoria(['stock' => 10]);
        $actualizado = $this->productoConCategoria(['stock' => 10]);
        $conStock = $this->productoConCategoria(['stock' => 15]);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($actual);
        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn($actual);
        $this->productos->shouldReceive('update')
            ->once()
            ->with($actual, Mockery::on(fn (array $payload): bool => ! array_key_exists('stock', $payload)))
            ->andReturn($actualizado);
        $this->productos->shouldReceive('update')
            ->once()
            ->with($actualizado, ['stock' => 15])
            ->andReturn($conStock);
        $this->movimientos->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => $payload['tipo_movimiento'] === 'entrada'
                && $payload['cantidad'] === 5
                && $payload['id_producto'] === 7
                && $payload['id_usuario'] === 4))
            ->andReturn($this->movimiento());

        $resultado = $this->service->actualizarProducto(7, $datos, 4);

        $this->assertSame(15, $resultado->existencia->stock);
    }

    public function test_actualizar_producto_reemplaza_la_imagen_anterior(): void
    {
        $datos = $this->datosProducto(['stock' => 10]);
        $archivo = UploadedFile::fake()->image('nueva.jpg');
        $actual = $this->productoConCategoria(['url_imagen' => 'productos/vieja.jpg']);
        $actualizado = $this->productoConCategoria(['url_imagen' => 'productos/nueva.jpg']);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($actual);
        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn($actual);
        $this->imagenes->shouldReceive('eliminar')->once()->with('productos/vieja.jpg');
        $this->imagenes->shouldReceive('guardar')->once()->with($archivo)->andReturn('productos/nueva.jpg');
        $this->productos->shouldReceive('update')
            ->once()
            ->with($actual, Mockery::on(fn (array $payload): bool => ($payload['url_imagen'] ?? null) === 'productos/nueva.jpg'))
            ->andReturn($actualizado);
        $this->movimientos->shouldReceive('create')->never();

        $resultado = $this->service->actualizarProducto(7, $datos, 4, $archivo);

        $this->assertSame('productos/nueva.jpg', $resultado->urlImagen);
    }

    public function test_actualizar_producto_falla_si_el_nombre_ya_existe(): void
    {
        $duplicado = $this->producto(['nombre' => 'Limonada']);
        $duplicado->id_producto = 9;

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($this->producto());
        $this->productos->shouldReceive('findByNombre')->once()->with('Limonada')->andReturn($duplicado);
        $this->productos->shouldReceive('update')->never();

        $this->expectException(ProductoNombreDuplicadoException::class);

        $this->service->actualizarProducto(7, $this->datosProducto(), 4);
    }

    public function test_eliminar_producto_borra_la_imagen(): void
    {
        $detalles = Mockery::mock(HasMany::class);
        $detalles->shouldReceive('exists')->once()->andReturn(false);
        $producto = Mockery::mock(Producto::class)->makePartial();
        $producto->url_imagen = 'productos/limonada.jpg';
        $producto->shouldReceive('detallesPedido')->once()->andReturn($detalles);

        $this->productos->shouldReceive('findById')->once()->with(7)->andReturn($producto);
        $this->imagenes->shouldReceive('eliminar')->once()->with('productos/limonada.jpg');
        $this->movimientos->shouldReceive('eliminarDeProducto')->once()->with(7);
        $this->productos->shouldReceive('delete')->once()->with($producto);

        $this->service->eliminarProducto(7);
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
            'id_categoria' => 2,
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
