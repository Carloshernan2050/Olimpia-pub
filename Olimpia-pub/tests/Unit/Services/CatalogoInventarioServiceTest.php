<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\Models\Categoria;
use App\Models\Producto;
use App\Services\CatalogoInventarioService;
use App\Support\Dashboard\UmbralStockInventario;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CatalogoInventarioServiceTest extends TestCase
{
    private ProductoRepositoryInterface&MockInterface $productos;

    private CategoriaRepositoryInterface&MockInterface $categorias;

    private MovimientoInventarioRepositoryInterface&MockInterface $movimientos;

    private CatalogoInventarioService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productos = Mockery::mock(ProductoRepositoryInterface::class);
        $this->categorias = Mockery::mock(CategoriaRepositoryInterface::class);
        $this->movimientos = Mockery::mock(MovimientoInventarioRepositoryInterface::class);
        $this->service = new CatalogoInventarioService(
            $this->productos,
            $this->categorias,
            $this->movimientos,
        );
    }

    public function test_catalogo_vacio_si_no_hay_productos(): void
    {
        $this->esperarConsultas(new LengthAwarePaginator(collect(), 0, 8, 1, [
            'path' => '/dashboard/inventario',
        ]));

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertFalse($catalogo->tieneProductos());
        $this->assertSame([], $catalogo->enOrden());
        $this->assertSame(0, $catalogo->resumen->productos);
        $this->assertSame(2, $catalogo->resumen->movimientos);
    }

    public function test_convierte_los_productos_en_filas(): void
    {
        $producto = new Producto([
            'nombre' => 'Limonada',
            'descripcion' => 'Natural',
            'precio' => '8.50',
            'stock' => 50,
            'estado' => 'activo',
        ]);
        $producto->id_producto = 7;
        $producto->setRelation('categoria', new Categoria(['nombre' => 'Bebidas']));

        $filtro = FiltroInventarioDatos::predeterminado();
        $this->esperarConsultas(
            new LengthAwarePaginator(collect([$producto]), 1, 8, 1, [
                'path' => '/dashboard/inventario',
            ]),
            $filtro,
            collect([$producto]),
        );

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $fila = $catalogo->enOrden()[0];

        $this->assertTrue($catalogo->tieneProductos());
        $this->assertInstanceOf(ProductoInventarioDatos::class, $fila);
        $this->assertSame(7, $fila->id);
        $this->assertSame('Limonada', $fila->nombre);
        $this->assertSame('Natural', $fila->detalle());
    }

    /**
     * @param  LengthAwarePaginator<int, Producto>  $paginador
     */
    private function esperarConsultas(
        LengthAwarePaginator $paginador,
        ?FiltroInventarioDatos $filtro = null,
        mixed $todos = null,
    ): void {
        $this->productos->shouldReceive('filtrar')
            ->once()
            ->with($filtro ? Mockery::on(fn (FiltroInventarioDatos $actual): bool => $actual === $filtro) : Mockery::type(FiltroInventarioDatos::class))
            ->andReturn($paginador);
        $this->productos->shouldReceive('resumenStock')
            ->once()
            ->with(UmbralStockInventario::BAJO)
            ->andReturn([
                'productos' => 0,
                'unidades' => 0,
                'bajo' => 0,
                'agotados' => 0,
            ]);
        $this->productos->shouldReceive('todos')->once()->andReturn($todos ?? collect());
        $this->categorias->shouldReceive('todas')->once()->andReturn(collect());
        $this->movimientos->shouldReceive('contar')->once()->andReturn(2);
    }
}
