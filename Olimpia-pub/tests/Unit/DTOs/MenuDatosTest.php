<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\CategoriaMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\ProductoMenuDatos;
use App\Models\Categoria;
use App\Models\Producto;
use Tests\TestCase;

class MenuDatosTest extends TestCase
{
    public function test_from_model_copia_nombre_y_precio_del_inventario(): void
    {
        $tarjeta = ProductoMenuDatos::fromModel($this->producto([
            'nombre' => 'Limonada',
            'precio' => '8.50',
        ]));

        $this->assertSame(3, $tarjeta->id);
        $this->assertSame('Limonada', $tarjeta->nombre);
        $this->assertSame('Bebidas', $tarjeta->categoria);
        $this->assertSame('$ 8,50', $tarjeta->precioFormateado());
    }

    public function test_refleja_el_precio_actualizado_del_inventario(): void
    {
        $tarjeta = ProductoMenuDatos::fromModel($this->producto([
            'nombre' => 'Limonada',
            'precio' => '15.00',
        ]));

        $this->assertSame('$ 15,00', $tarjeta->precioFormateado());
    }

    public function test_catalogo_vacio_no_tiene_productos(): void
    {
        $catalogo = new CatalogoMenuDatos([], []);

        $this->assertFalse($catalogo->tieneProductos());
        $this->assertSame([], $catalogo->enOrden());
    }

    public function test_filtro_predeterminado_no_esta_activo(): void
    {
        $filtro = FiltroMenuDatos::predeterminado();

        $this->assertFalse($filtro->estaActivo());
        $this->assertNull($filtro->busqueda);
        $this->assertSame([], $filtro->query());
    }

    public function test_filtro_ignora_valores_invalidos(): void
    {
        $filtro = FiltroMenuDatos::fromInput('  Cola  ', 'abc');

        $this->assertSame('Cola', $filtro->busqueda);
        $this->assertNull($filtro->idCategoria);
        $this->assertTrue($filtro->estaActivo());
        $this->assertSame(['busqueda' => 'Cola'], $filtro->query());
    }

    public function test_filtro_conserva_categoria_en_enlaces(): void
    {
        $filtro = FiltroMenuDatos::fromInput(null, '4');

        $this->assertSame(4, $filtro->idCategoria);
        $this->assertSame(['categoria' => 4], $filtro->query());
        $this->assertSame([], $filtro->queryConCategoria(null));
        $this->assertSame(['categoria' => 2], $filtro->queryConCategoria(2));
    }

    public function test_categoria_copia_id_nombre_e_icono(): void
    {
        $categoria = new Categoria(['nombre' => 'Bebidas']);
        $categoria->id_categoria = 8;
        $datos = CategoriaMenuDatos::fromModel($categoria, 'taza');

        $this->assertSame(8, $datos->id);
        $this->assertSame('Bebidas', $datos->nombre);
        $this->assertSame('taza', $datos->icono);
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
