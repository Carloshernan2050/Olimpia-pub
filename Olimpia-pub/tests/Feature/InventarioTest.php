<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_invitado_no_puede_ver_inventario(): void
    {
        $this->get(route('inventario'))->assertRedirect(route('iniciar-sesion'));
    }

    public function test_sin_productos_muestra_el_encabezado_y_el_vacio(): void
    {
        $this->autenticarConRol('empleado');

        $this->get(route('inventario'))
            ->assertOk()
            ->assertSee('Inventario —', false)
            ->assertSee('id="titulo-inventario"', false)
            ->assertSee('Productos')
            ->assertSee('Movimientos')
            ->assertSee('Stock bajo')
            ->assertSee('Agotados')
            ->assertSee('Buscar')
            ->assertSee('data-filtro-inventario', false)
            ->assertSee('aria-label="Agregar producto"', false)
            ->assertSee('<span>Agregar producto</span>', false)
            ->assertSee('No hay productos en el inventario.')
            ->assertDontSee('inventario-tabla', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_el_invitado_no_puede_registrar_movimientos(): void
    {
        $this->post(route('inventario.guardar'), $this->datosMovimiento())
            ->assertRedirect(route('iniciar-sesion'));

        $this->post(route('inventario.producto.guardar'), $this->datosProducto())
            ->assertRedirect(route('iniciar-sesion'));
    }

    public function test_el_cliente_no_puede_ver_inventario(): void
    {
        $this->autenticar();

        $this->get(route('inventario'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'No tienes permiso para acceder al inventario.');

        $this->post(route('inventario.guardar'), $this->datosMovimiento())
            ->assertRedirect(route('dashboard'));

        $this->post(route('inventario.producto.guardar'), $this->datosProducto())
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('aria-label="Inventario"', false)
            ->assertDontSee('href="'.route('inventario').'"', false);
    }

    public function test_el_administrador_puede_ver_inventario(): void
    {
        $this->autenticarConRol('administrador', 'admin@olimpia.com');

        $this->get(route('inventario'))
            ->assertOk()
            ->assertSee('id="titulo-inventario"', false);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('aria-label="Inventario"', false)
            ->assertSee('href="'.route('inventario').'"', false);
    }

    public function test_lista_productos_y_registra_una_entrada(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['nombre' => 'Limonada', 'stock' => 10]);

        $this->get(route('inventario'))
            ->assertOk()
            ->assertSee('inventario-tabla', false)
            ->assertSee('Limonada')
            ->assertSee('Bebidas')
            ->assertSee('Disponible');

        $this->post(route('inventario.guardar'), $this->datosMovimiento([
            'id_producto' => $producto->id_producto,
            'cantidad' => 5,
        ]))
            ->assertRedirect(route('inventario'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('producto', [
            'id_producto' => $producto->id_producto,
            'stock' => 15,
        ]);
        $this->assertDatabaseHas('movimiento_inventario', [
            'id_producto' => $producto->id_producto,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 5,
        ]);

        $this->get(route('inventario'))
            ->assertOk()
            ->assertSee('Movimiento registrado correctamente.')
            ->assertSee('data-aviso', false);
    }

    public function test_validacion_reabre_el_modal_sin_guardar(): void
    {
        $this->autenticarConRol('empleado');

        $this->from(route('inventario'))
            ->followingRedirects()
            ->post(route('inventario.guardar'), [
                'formulario' => 'movimiento',
                'id_producto' => '',
                'tipo_movimiento' => '',
                'cantidad' => '',
            ])
            ->assertOk()
            ->assertSee('El producto es obligatorio.')
            ->assertSee('data-abrir', false);

        $this->assertDatabaseCount('movimiento_inventario', 0);
    }

    public function test_crea_un_producto_con_stock_inicial(): void
    {
        $this->autenticarConRol('empleado');
        $categoria = $this->categoria();

        $this->post(route('inventario.producto.guardar'), $this->datosProducto([
            'id_categoria' => $categoria->id_categoria,
        ]))
            ->assertRedirect(route('inventario'))
            ->assertSessionHas('exito', 'Producto creado correctamente.');

        $this->assertDatabaseHas('producto', [
            'nombre' => 'Limonada de casa',
            'precio' => '12.50',
            'stock' => 8,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
        ]);
        $this->assertDatabaseHas('movimiento_inventario', [
            'tipo_movimiento' => 'entrada',
            'cantidad' => 8,
        ]);

        $this->get(route('inventario'))
            ->assertOk()
            ->assertSee('Limonada de casa')
            ->assertSee('Producto creado correctamente.');
    }

    public function test_el_lapiz_abre_el_formulario_de_movimiento(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['nombre' => 'Limonada']);

        $this->get(route('inventario', ['producto' => $producto->id_producto]))
            ->assertOk()
            ->assertSee('Registrar movimiento')
            ->assertSee('Selecciona un producto')
            ->assertDontSee('id="inventario-nombre"', false)
            ->assertSee('data-abrir', false);
    }

    public function test_validacion_de_producto_reabre_el_alta_sin_guardar(): void
    {
        $this->autenticarConRol('empleado');

        $this->from(route('inventario'))
            ->followingRedirects()
            ->post(route('inventario.producto.guardar'), [
                'formulario' => 'producto',
                'nombre' => '',
                'precio' => '',
                'stock' => '',
                'id_categoria' => '',
            ])
            ->assertOk()
            ->assertSee('El nombre es obligatorio.')
            ->assertSee('id="inventario-nombre"', false)
            ->assertSee('data-abrir', false);

        $this->assertDatabaseCount('producto', 0);
    }

    public function test_actualiza_y_elimina_un_movimiento(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['stock' => 10]);
        $movimiento = $this->crearMovimiento($producto, ['cantidad' => 5]);
        $producto->update(['stock' => 15]);

        $this->put(route('inventario.actualizar', $movimiento->id_movimiento), $this->datosMovimiento([
            'id_producto' => $producto->id_producto,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 8,
        ]))
            ->assertRedirect(route('inventario'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('producto', ['id_producto' => $producto->id_producto, 'stock' => 18]);
        $this->assertDatabaseHas('movimiento_inventario', [
            'id_movimiento' => $movimiento->id_movimiento,
            'cantidad' => 8,
        ]);

        $this->delete(route('inventario.eliminar', $movimiento->id_movimiento))
            ->assertRedirect(route('inventario'));

        $this->assertDatabaseMissing('movimiento_inventario', [
            'id_movimiento' => $movimiento->id_movimiento,
        ]);
        $this->assertDatabaseHas('producto', ['id_producto' => $producto->id_producto, 'stock' => 10]);
    }

    public function test_filtra_por_busqueda_y_estado(): void
    {
        $this->autenticarConRol('empleado');
        $this->crearProducto(['nombre' => 'Limonada', 'stock' => 50]);
        $this->crearProducto(['nombre' => 'Brownie', 'stock' => 0]);

        $this->get(route('inventario', ['busqueda' => 'Limona']))
            ->assertOk()
            ->assertSee('aria-label="Ver Limonada"', false)
            ->assertDontSee('aria-label="Ver Brownie"', false);

        $this->get(route('inventario', ['estado' => 'agotado']))
            ->assertOk()
            ->assertSee('aria-label="Ver Brownie"', false)
            ->assertDontSee('aria-label="Ver Limonada"', false);
    }

    public function test_abre_el_detalle_del_producto(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['nombre' => 'Hamburguesa clásica', 'stock' => 4]);

        $this->get(route('inventario', ['ver' => $producto->id_producto]))
            ->assertOk()
            ->assertSee('Hamburguesa clásica')
            ->assertSee('Stock:')
            ->assertSee('data-abrir', false);
    }

    public function test_elimina_un_producto_sin_pedidos(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['nombre' => 'Brownie']);

        $this->delete(route('inventario.producto.eliminar', $producto->id_producto))
            ->assertRedirect(route('inventario'))
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('producto', ['id_producto' => $producto->id_producto]);
    }

    public function test_salida_sin_stock_muestra_el_aviso(): void
    {
        $this->autenticarConRol('empleado');
        $producto = $this->crearProducto(['stock' => 2]);

        $this->from(route('inventario'))
            ->post(route('inventario.guardar'), $this->datosMovimiento([
                'id_producto' => $producto->id_producto,
                'tipo_movimiento' => 'salida',
                'cantidad' => 5,
            ]))
            ->assertRedirect(route('inventario'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('producto', [
            'id_producto' => $producto->id_producto,
            'stock' => 2,
        ]);
        $this->assertDatabaseCount('movimiento_inventario', 0);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function datosProducto(array $extra = []): array
    {
        return [
            'formulario' => 'producto',
            'nombre' => 'Limonada de casa',
            'descripcion' => 'Con hierbabuena',
            'precio' => '12.50',
            'stock' => 8,
            'id_categoria' => 1,
            'estado' => 'activo',
            ...$extra,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function datosMovimiento(array $extra = []): array
    {
        return [
            'formulario' => 'movimiento',
            'id_producto' => 1,
            'tipo_movimiento' => 'entrada',
            'cantidad' => 1,
            ...$extra,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearProducto(array $extra = []): Producto
    {
        $categoria = $this->categoria();

        return Producto::query()->create([
            'nombre' => 'Producto',
            'descripcion' => 'Detalle',
            'precio' => 10,
            'stock' => 20,
            'estado' => 'activo',
            'id_categoria' => $categoria->id_categoria,
            ...$extra,
        ]);
    }

    private function categoria(): Categoria
    {
        return Categoria::query()->first() ?? Categoria::query()->create([
            'nombre' => 'Bebidas',
            'descripcion' => 'Bebidas frías y calientes',
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearMovimiento(Producto $producto, array $extra = []): MovimientoInventario
    {
        return MovimientoInventario::query()->create([
            'tipo_movimiento' => 'entrada',
            'cantidad' => 5,
            'fecha' => now(),
            'id_producto' => $producto->id_producto,
            'id_usuario' => auth()->id(),
            ...$extra,
        ]);
    }
}
