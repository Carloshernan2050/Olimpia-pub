<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoInventarioDatos;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\DTOs\Dashboard\GuardarMovimientoInventarioDatos;
use App\DTOs\Dashboard\GuardarProductoInventarioDatos;
use App\DTOs\Dashboard\InventarioExistenciaDatos;
use App\DTOs\Dashboard\MovimientoInventarioGestionDatos;
use App\DTOs\Dashboard\PaginacionInventarioDatos;
use App\DTOs\Dashboard\ProductoInventarioDatos;
use App\DTOs\Dashboard\ResumenInventarioDatos;
use App\Enums\EstadoStockInventario;
use App\Enums\TipoMovimientoInventario;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
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
        $this->assertSame(2, $fila->idCategoria);
        $this->assertSame(4, $fila->existencia->stock);
        $this->assertSame('activo', $fila->existencia->estado);
        $this->assertSame(EstadoStockInventario::Bajo, $fila->existencia->estadoStock);
        $this->assertSame('Stock bajo', $fila->etiquetaEstadoStock());
        $this->assertSame('8,50', $fila->precioFormateado());
        $this->assertTrue($fila->estaActivo());
        $this->assertFalse($fila->tieneImagen());
    }

    public function test_from_model_copia_la_imagen_del_producto(): void
    {
        $fila = ProductoInventarioDatos::fromModel($this->producto([
            'url_imagen' => 'productos/limonada.jpg',
        ]));

        $this->assertTrue($fila->tieneImagen());
        $this->assertSame('/storage/productos/limonada.jpg', $fila->urlImagenPublica());
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

    public function test_filtro_conserva_categoria_y_pagina_validas(): void
    {
        $filtro = FiltroInventarioDatos::fromInput(null, '4', 'agotado', 3);

        $this->assertSame(4, $filtro->idCategoria);
        $this->assertSame(EstadoStockInventario::Agotado, $filtro->estadoStock);
        $this->assertSame(3, $filtro->pagina);
        $this->assertTrue($filtro->estaActivo());
        $this->assertSame(['categoria' => 4, 'estado' => 'agotado'], $filtro->query());
    }

    public function test_existencia_copia_stock_y_estado_del_modelo(): void
    {
        $existencia = InventarioExistenciaDatos::fromModel($this->producto(['stock' => 0, 'estado' => 'activo']));

        $this->assertSame(0, $existencia->stock);
        $this->assertSame('activo', $existencia->estado);
        $this->assertSame(EstadoStockInventario::Agotado, $existencia->estadoStock);
    }

    public function test_movimiento_sin_tipo_ni_producto_usa_valores_por_defecto(): void
    {
        $movimiento = new MovimientoInventario([
            'tipo_movimiento' => 'otro',
            'cantidad' => 2,
            'fecha' => null,
            'id_producto' => 3,
        ]);
        $movimiento->id_movimiento = 4;
        $movimiento->setRelation('producto', null);

        $datos = MovimientoInventarioGestionDatos::fromModel($movimiento);

        $this->assertSame(TipoMovimientoInventario::Entrada, $datos->tipo);
        $this->assertSame('Producto', $datos->nombreProducto);
        $this->assertSame('', $datos->fecha);
        $this->assertSame('Entrada', $datos->etiquetaTipo());
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
        $this->assertSame(0, $datos->paraCrear()['stock']);
        $this->assertArrayNotHasKey('stock', $datos->paraActualizar());
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
            'id_categoria' => 2,
            ...$extra,
        ]);
        $producto->id_producto = 3;
        $producto->setRelation('categoria', new Categoria(['nombre' => 'Bebidas']));

        return $producto;
    }
}
