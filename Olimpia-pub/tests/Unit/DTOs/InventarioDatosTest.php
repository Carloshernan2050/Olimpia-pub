<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoInventarioDatos;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\PaginacionInventarioDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\DTOs\Dashboard\ResumenInventarioDatos;
use App\Enums\EstadoStockInventario;
use App\Enums\TipoMovimientoInventario;
use App\Models\Categoria;
use App\Models\Producto;
use Tests\TestCase;

class InventarioDatosTest extends TestCase
{
    public function test_from_model_copia_stock_categoria_y_estado(): void
    {
        $producto = $this->producto(['nombre' => 'Limonada', 'stock' => 4, 'precio' => '8.50']);
        $fila = ProductoInventarioDatos::fromModel($producto);

        $this->assertSame(3, $fila->id);
        $this->assertSame('Limonada', $fila->nombre);
        $this->assertSame('Bebidas', $fila->categoria);
        $this->assertSame(EstadoStockInventario::Bajo, $fila->estadoStock);
        $this->assertSame('Stock bajo', $fila->etiquetaEstadoStock());
        $this->assertSame('8,50', $fila->precioFormateado());
        $this->assertTrue($fila->estaActivo());
    }

    public function test_sin_descripcion_usa_la_categoria_como_detalle(): void
    {
        $fila = ProductoInventarioDatos::fromModel($this->producto(['descripcion' => null]));

        $this->assertSame('Bebidas', $fila->detalle());
    }

    public function test_catalogo_vacio_no_tiene_productos(): void
    {
        $catalogo = new CatalogoInventarioDatos(
            [],
            new ResumenInventarioDatos(0, 0, 0, 0),
            new PaginacionInventarioDatos(1, 1, null, null),
            [],
            [],
        );

        $this->assertFalse($catalogo->tieneProductos());
        $this->assertFalse($catalogo->paginacion->hayMasDeUnaPagina());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_filtro_predeterminado_no_esta_activo(): void
    {
        $filtro = FiltroInventarioDatos::predeterminado();

        $this->assertFalse($filtro->estaActivo());
        $this->assertNull($filtro->busqueda);
        $this->assertSame(1, $filtro->pagina);
        $this->assertSame([], $filtro->query());
    }

    public function test_filtro_ignora_valores_invalidos(): void
    {
        $filtro = FiltroInventarioDatos::fromInput('  Cola  ', 'abc', 'vencido', 0);

        $this->assertSame('Cola', $filtro->busqueda);
        $this->assertNull($filtro->idCategoria);
        $this->assertNull($filtro->estadoStock);
        $this->assertSame(1, $filtro->pagina);
        $this->assertTrue($filtro->estaActivo());
        $this->assertSame(['busqueda' => 'Cola'], $filtro->query());
    }

    public function test_guardar_movimiento_normaliza_tipo_y_cantidad(): void
    {
        $datos = GuardarMovimientoInventarioDatos::fromValidated([
            'id_producto' => '7',
            'tipo_movimiento' => 'salida',
            'cantidad' => '3',
        ]);

        $this->assertSame(7, $datos->idProducto);
        $this->assertSame(TipoMovimientoInventario::Salida, $datos->tipo);
        $this->assertSame(3, $datos->cantidad);
        $this->assertSame(4, $datos->paraCrear(4)['id_usuario']);
        $this->assertArrayNotHasKey('id_usuario', $datos->paraActualizar());
    }

    public function test_guardar_producto_normaliza_nombre_stock_y_estado(): void
    {
        $datos = GuardarProductoInventarioDatos::fromValidated([
            'nombre' => '  Limonada  ',
            'descripcion' => '   ',
            'precio' => '8.50',
            'stock' => -3,
            'id_categoria' => '2',
            'estado' => 'pausado',
        ]);

        $this->assertSame('Limonada', $datos->nombre);
        $this->assertNull($datos->descripcion);
        $this->assertSame(0, $datos->stock);
        $this->assertSame(2, $datos->idCategoria);
        $this->assertSame('activo', $datos->estado);
        $this->assertSame('Limonada', $datos->paraCrear()['nombre']);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function producto(array $extra = []): Producto
    {
        $producto = new Producto([
            'nombre' => 'Cola',
            'descripcion' => '350 ml',
            'precio' => '3.50',
            'stock' => 12,
            'estado' => 'activo',
            ...$extra,
        ]);
        $producto->id_producto = 3;
        $producto->setRelation('categoria', new Categoria(['nombre' => 'Bebidas']));

        return $producto;
    }
}
