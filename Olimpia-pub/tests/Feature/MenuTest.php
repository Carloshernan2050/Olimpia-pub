<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_invitado_no_puede_ver_el_menu(): void
    {
        $this->get(route('menu'))->assertRedirect(route('iniciar-sesion'));
    }

    public function test_sin_productos_muestra_el_encabezado_y_el_vacio(): void
    {
        $this->autenticar();

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('Menú —', false)
            ->assertSee('id="titulo-menu"', false)
            ->assertSee('Filtrar')
            ->assertSee('Buscar')
            ->assertSee('Categoría')
            ->assertSee('aria-label="Todas las categorías"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('No hay productos en el menú.')
            ->assertSee('Realizar pedido')
            ->assertSee('aria-label="Realizar pedido"', false)
            ->assertDontSee('grilla-menu', false)
            ->assertDontSee('menu-tarjeta', false)
            ->assertSee('href="'.route('menu').'"', false)
            ->assertSee('aria-label="Comida y bebida"', false);
    }

    public function test_muestra_nombre_precio_e_imagen_sin_controles_de_compra(): void
    {
        $this->autenticar();
        $this->crearProducto([
            'nombre' => 'Limonada',
            'precio' => '12.50',
            'stock' => 8,
        ]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('grilla-menu', false)
            ->assertSee('Limonada')
            ->assertSee('$ 12,50')
            ->assertSee('menu-tarjeta-imagen', false)
            ->assertSee('Realizar pedido')
            ->assertDontSee('Stock: 8')
            ->assertDontSee('Agregar')
            ->assertDontSee('selector-cantidad', false)
            ->assertDontSee('No hay productos en el menú.');
    }

    public function test_el_precio_del_menu_sigue_al_inventario(): void
    {
        $this->autenticar();
        $producto = $this->crearProducto([
            'nombre' => 'Limonada',
            'precio' => '10.00',
            'stock' => 5,
        ]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('$ 10,00');

        $producto->update(['precio' => '18.75']);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('$ 18,75')
            ->assertDontSee('$ 10,00');
    }

    public function test_un_producto_creado_en_inventario_aparece_en_el_menu(): void
    {
        $this->autenticarConRol('empleado');
        $categoria = $this->categoria();

        $this->post(route('inventario.producto.guardar'), [
            'formulario' => 'producto',
            'nombre' => 'Picada hincha',
            'descripcion' => 'Para compartir',
            'precio' => '22.00',
            'stock' => 6,
            'id_categoria' => $categoria->id_categoria,
            'estado' => 'activo',
        ])->assertRedirect(route('inventario'));

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('Picada hincha')
            ->assertSee('$ 22,00')
            ->assertDontSee('Stock: 6');
    }

    public function test_no_muestra_productos_inactivos(): void
    {
        $this->autenticar();
        $this->crearProducto(['nombre' => 'Activa', 'estado' => 'activo']);
        $this->crearProducto(['nombre' => 'Oculta', 'estado' => 'inactivo']);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('Activa')
            ->assertDontSee('Oculta');
    }

    public function test_filtra_por_categoria_y_busqueda(): void
    {
        $this->autenticar();
        $bebidas = $this->categoria(['nombre' => 'Bebidas']);
        $comidas = $this->categoria(['nombre' => 'Comidas']);
        $this->crearProducto([
            'nombre' => 'Limonada',
            'id_categoria' => $bebidas->id_categoria,
        ]);
        $this->crearProducto([
            'nombre' => 'Hamburguesa',
            'id_categoria' => $comidas->id_categoria,
        ]);

        $this->get(route('menu', ['categoria' => $bebidas->id_categoria]))
            ->assertOk()
            ->assertSee('Limonada')
            ->assertDontSee('Hamburguesa')
            ->assertSee('aria-current="true"', false);

        $this->get(route('menu', ['busqueda' => 'Hambur']))
            ->assertOk()
            ->assertSee('Hamburguesa')
            ->assertDontSee('Limonada');
    }

    public function test_el_producto_agotado_sigue_visible_en_el_catalogo(): void
    {
        $this->autenticar();
        $this->crearProducto(['nombre' => 'Agua', 'precio' => '2.00', 'stock' => 0]);

        $this->get(route('menu'))
            ->assertOk()
            ->assertSee('Agua')
            ->assertSee('$ 2,00')
            ->assertDontSee('Agotado')
            ->assertDontSee('disabled', false);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearProducto(array $extra = []): Producto
    {
        $categoria = isset($extra['id_categoria'])
            ? Categoria::query()->findOrFail($extra['id_categoria'])
            : $this->categoria();

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

    /**
     * @param  array<string, mixed>  $extra
     */
    private function categoria(array $extra = []): Categoria
    {
        $nombre = $extra['nombre'] ?? 'Bebidas';

        return Categoria::query()->firstWhere('nombre', $nombre)
            ?? Categoria::query()->create([
                'nombre' => $nombre,
                'descripcion' => 'Categoría de menú',
                ...$extra,
            ]);
    }
}
