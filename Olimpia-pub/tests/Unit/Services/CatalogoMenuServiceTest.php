<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\ProductoMenuDatos;
use App\Models\Categoria;
use App\Models\Producto;
use App\Services\CatalogoMenuService;
use App\Support\Dashboard\IconoCategoriaMenu;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CatalogoMenuServiceTest extends TestCase
{
    private ProductoRepositoryInterface&MockInterface $productos;

    private CategoriaRepositoryInterface&MockInterface $categorias;

    private CatalogoMenuService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productos = Mockery::mock(ProductoRepositoryInterface::class);
        $this->categorias = Mockery::mock(CategoriaRepositoryInterface::class);
        $this->service = new CatalogoMenuService(
            $this->productos,
            $this->categorias,
            new IconoCategoriaMenu,
        );
    }

    public function test_catalogo_vacio_si_no_hay_productos(): void
    {
        $this->productos->shouldReceive('paraMenu')
            ->once()
            ->with(Mockery::type(FiltroMenuDatos::class))
            ->andReturn(collect());
        $this->categorias->shouldReceive('todas')->once()->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertFalse($catalogo->tieneProductos());
        $this->assertSame([], $catalogo->enOrden());
        $this->assertSame([], $catalogo->categorias);
    }

    public function test_convierte_los_productos_de_inventario_en_tarjetas(): void
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

        $bebidas = new Categoria(['nombre' => 'Bebidas']);
        $bebidas->id_categoria = 2;
        $comidas = new Categoria(['nombre' => 'Comidas']);
        $comidas->id_categoria = 1;

        $filtro = FiltroMenuDatos::predeterminado();
        $this->productos->shouldReceive('paraMenu')
            ->once()
            ->with($filtro)
            ->andReturn(Collection::make([$producto]));
        $this->categorias->shouldReceive('todas')
            ->once()
            ->andReturn(Collection::make([$bebidas, $comidas]));

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $tarjeta = $catalogo->enOrden()[0];

        $this->assertTrue($catalogo->tieneProductos());
        $this->assertInstanceOf(ProductoMenuDatos::class, $tarjeta);
        $this->assertSame(7, $tarjeta->id);
        $this->assertSame('Limonada', $tarjeta->nombre);
        $this->assertSame('$ 8,50', $tarjeta->precioFormateado());
        $this->assertSame('Comidas', $catalogo->categorias[0]->nombre);
        $this->assertSame('estrella', $catalogo->categorias[0]->icono);
        $this->assertSame('Bebidas', $catalogo->categorias[1]->nombre);
        $this->assertSame('taza', $catalogo->categorias[1]->icono);
    }
}
