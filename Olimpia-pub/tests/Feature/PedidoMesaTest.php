<?php

namespace Tests\Feature;

use App\Enums\EstadoPedido;
use App\Enums\TipoMesa;
use App\Models\Categoria;
use App\Models\CodigoQr;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoMesaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_invitado_ve_el_menu_de_la_mesa_al_abrir_el_qr(): void
    {
        $this->crearMesa(3);
        $this->crearProducto(['nombre' => 'Limonada', 'precio' => '12.50']);

        $this->get(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-03']))
            ->assertOk()
            ->assertSee('Mesa 3')
            ->assertSee('Menú')
            ->assertSee('Limonada')
            ->assertSee('$ 12,50')
            ->assertSee('Agregar')
            ->assertSee('Enviar pedido')
            ->assertSee('href="'.route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-03']).'"', false)
            ->assertDontSee('Realizar pedido');
    }

    public function test_un_codigo_desconocido_muestra_que_la_mesa_no_existe(): void
    {
        $this->get(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-99']))
            ->assertNotFound()
            ->assertSee('Mesa no encontrada')
            ->assertSee('La mesa no existe.');
    }

    public function test_filtra_el_menu_publico_por_categoria(): void
    {
        $this->crearMesa(1);
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

        $this->get(route('mesa.menu', [
            'codigo' => 'OLIMPIA-MESA-01',
            'categoria' => $bebidas->id_categoria,
        ]))
            ->assertOk()
            ->assertSee('Limonada')
            ->assertDontSee('Hamburguesa');
    }

    public function test_el_cliente_envia_el_pedido_a_la_mesa(): void
    {
        $mesa = $this->crearMesa(2);
        $producto = $this->crearProducto(['nombre' => 'Limonada', 'precio' => '8.50']);

        $this->from(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-02']))
            ->post(route('mesa.pedir', ['codigo' => 'OLIMPIA-MESA-02']), [
                'lineas' => [
                    ['id_producto' => $producto->id_producto, 'cantidad' => 2],
                ],
            ])
            ->assertRedirect(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-02']))
            ->assertSessionHas('exito', 'Pedido enviado a la mesa.');

        $this->assertDatabaseHas('pedido', [
            'id_mesa' => $mesa->id_mesa,
            'estado' => EstadoPedido::Activo->value,
            'total' => '17.00',
        ]);
        $this->assertDatabaseHas('detalle_pedido', [
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
            'subtotal' => '17.00',
        ]);
    }

    public function test_un_segundo_envio_suma_productos_al_pedido_activo(): void
    {
        $mesa = $this->crearMesa(4);
        $limonada = $this->crearProducto(['nombre' => 'Limonada', 'precio' => '8.50']);
        $agua = $this->crearProducto(['nombre' => 'Agua', 'precio' => '2.00']);

        $this->post(route('mesa.pedir', ['codigo' => 'OLIMPIA-MESA-04']), [
            'lineas' => [
                ['id_producto' => $limonada->id_producto, 'cantidad' => 1],
            ],
        ])->assertRedirect(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-04']));

        $this->post(route('mesa.pedir', ['codigo' => 'OLIMPIA-MESA-04']), [
            'lineas' => [
                ['id_producto' => $agua->id_producto, 'cantidad' => 2],
            ],
        ])->assertRedirect(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-04']));

        $this->assertSame(1, Pedido::query()->where('id_mesa', $mesa->id_mesa)->count());
        $this->assertDatabaseHas('pedido', [
            'id_mesa' => $mesa->id_mesa,
            'total' => '12.50',
        ]);
        $this->assertDatabaseHas('detalle_pedido', [
            'id_producto' => $agua->id_producto,
            'cantidad' => 2,
        ]);
    }

    public function test_exige_al_menos_un_producto_para_pedir(): void
    {
        $this->crearMesa(5);

        $this->from(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-05']))
            ->post(route('mesa.pedir', ['codigo' => 'OLIMPIA-MESA-05']), [])
            ->assertRedirect(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-05']))
            ->assertSessionHasErrors('lineas');
    }

    public function test_el_pedido_del_qr_aparece_en_el_tablero_de_mesas(): void
    {
        $this->autenticar();
        $this->crearMesa(6);
        $producto = $this->crearProducto(['nombre' => 'Picada', 'precio' => '22.00']);

        $this->post(route('mesa.pedir', ['codigo' => 'OLIMPIA-MESA-06']), [
            'lineas' => [
                ['id_producto' => $producto->id_producto, 'cantidad' => 1],
            ],
        ])->assertRedirect(route('mesa.menu', ['codigo' => 'OLIMPIA-MESA-06']));

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Picada')
            ->assertSee('$ 22,00');
    }

    private function crearMesa(int $numero): Mesa
    {
        $codigo = CodigoQr::query()->create([
            'numero_qr' => $numero,
            'estado' => 'activo',
            'codigo_qr' => 'OLIMPIA-MESA-'.str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
        ]);

        return Mesa::query()->create([
            'numero_mesa' => $numero,
            'tipo' => TipoMesa::Mesa->value,
            'estado' => 'disponible',
            'id_qr' => $codigo->id_qr,
        ]);
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
