<?php

namespace Tests\Feature;

use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\Enums\AccionHistorial;
use App\Enums\EstadoPedido;
use App\Enums\TipoMesa;
use App\Exceptions\Mesa\PedidoActivoYaExisteException;
use App\Models\Categoria;
use App\Models\CodigoQr;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
    }

    public function test_el_invitado_no_puede_ver_mesas(): void
    {
        $this->get(route('mesas'))->assertRedirect(route('iniciar-sesion'));
    }

    public function test_sin_mesas_muestra_el_encabezado_y_el_vacio(): void
    {
        $this->autenticar();

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Mesas —', false)
            ->assertSee('id="titulo-mesas"', false)
            ->assertSee('aria-label="Barra"', false)
            ->assertSee('mesas-tipo-nombre', false)
            ->assertSee('Barra')
            ->assertSee('is-mesa', false)
            ->assertSee('Mesa')
            ->assertSee('aria-label="Grupo"', false)
            ->assertSee('Grupo')
            ->assertSee('aria-label="Pedidos activos"', false)
            ->assertSee('Pedidos activos')
            ->assertSee('is-pedidos-activos', false)
            ->assertDontSee('aria-label="Todos"', false)
            ->assertDontSee('aria-label="Pareja"', false)
            ->assertSee('No hay mesas en el catálogo.')
            ->assertSee('id="mesa-numero"', false)
            ->assertSee('value="1"', false)
            ->assertSee('aria-label="Añadir mesa"', false)
            ->assertSee('href="'.route('mesas').'"', false)
            ->assertSee('aria-label="Mesas"', false)
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('mesas-tabla', false)
            ->assertDontSee('Unir mesas')
            ->assertDontSee('Liberar mesas');
    }

    public function test_lista_el_pedido_compacto_la_cuenta_y_las_acciones(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($mesa, 'Limonada', 2, '8.50');

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('mesas-tabla', false)
            ->assertSee('Mesa 2')
            ->assertSee('Limonada')
            ->assertSee('$ 17,00')
            ->assertSee('aria-label="Ver pedido de Mesa 2"', false)
            ->assertSee('aria-label="Terminar pedido de Mesa 2"', false)
            ->assertSee('aria-label="Ver Mesa 2"', false)
            ->assertSee('aria-label="Editar Mesa 2"', false)
            ->assertSee('aria-label="Eliminar Mesa 2"', false)
            ->assertSee('aria-label="Añadir mesa"', false)
            ->assertDontSee('No hay mesas en el catálogo.');
    }

    public function test_el_pedido_se_abre_en_el_modal(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($mesa, 'Limonada', 2, '8.50');

        $this->get(route('mesas', ['pedido' => $mesa->id_mesa]))
            ->assertOk()
            ->assertSee('Pedido de Mesa 2')
            ->assertSee('2 × $ 8,50')
            ->assertSee('data-abrir', false);
    }

    public function test_ver_muestra_el_qr_en_el_modal(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(3, TipoMesa::Barra);

        $this->get(route('mesas', ['ver' => $mesa->id_mesa]))
            ->assertOk()
            ->assertSee('OLIMPIA-MESA-03')
            ->assertSee('<svg', false)
            ->assertSee('Libre')
            ->assertSee('data-abrir', false);
    }

    public function test_filtra_por_tipo_de_mesa(): void
    {
        $this->autenticar();
        $this->crearMesa(1, TipoMesa::Barra);
        $this->crearMesa(2, TipoMesa::Mesa);

        $this->get(route('mesas', ['tipo' => 'barra']))
            ->assertOk()
            ->assertSee('Mesa 1')
            ->assertDontSee('Mesa 2')
            ->assertSee('aria-label="Todos"', false)
            ->assertSee('Todos')
            ->assertSee('is-activa', false)
            ->assertDontSee('aria-label="Barra"', false)
            ->assertSee('href="'.route('mesas').'"', false)
            ->assertSee('aria-current="true"', false);
    }

    public function test_filtra_mesas_con_pedido_activo(): void
    {
        $this->autenticar();
        $this->crearMesa(1, TipoMesa::Barra);
        $ocupada = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($ocupada, 'Limonada', 1, '8.50');

        $this->get(route('mesas', ['tipo' => 'pedidos-activos']))
            ->assertOk()
            ->assertSee('Mesa 2')
            ->assertDontSee('Mesa 1')
            ->assertSee('Limonada')
            ->assertSee('aria-label="Todos"', false)
            ->assertSee('Todos')
            ->assertSee('is-activa', false)
            ->assertSee('is-pedidos-activos', false)
            ->assertDontSee('aria-label="Pedidos activos"', false)
            ->assertDontSee('No hay pedidos activos.');
    }

    public function test_filtra_grupos_con_pedido_activo(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($primera, 'Limonada', 1, '8.50');

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $this->get(route('mesas', ['tipo' => 'pedidos-activos']))
            ->assertOk()
            ->assertSee('Mesas 1 y 2')
            ->assertDontSee('No hay pedidos activos.');
    }

    public function test_sin_pedidos_activos_muestra_el_vacio(): void
    {
        $this->autenticar();
        $this->crearMesa(1, TipoMesa::Barra);

        $this->get(route('mesas', ['tipo' => 'pedidos-activos']))
            ->assertOk()
            ->assertSee('No hay pedidos activos.')
            ->assertDontSee('Mesa 1');
    }

    public function test_el_formulario_asigna_el_siguiente_numero(): void
    {
        $this->autenticar();
        $this->crearMesa(1, TipoMesa::Barra);
        $mesa = $this->crearMesa(4, TipoMesa::Mesa);

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('id="mesa-numero"', false)
            ->assertSee('value="5"', false);

        $this->get(route('mesas', ['editar' => $mesa->id_mesa]))
            ->assertOk()
            ->assertSee('value="4"', false);
    }

    public function test_crea_una_mesa_con_su_codigo(): void
    {
        $this->autenticar();

        $this->post(route('mesas.guardar'), [
            'formulario' => 'mesa',
            'numero_mesa' => 21,
            'tipo' => TipoMesa::Mesa->value,
        ])->assertRedirect(route('mesas'));

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Mesa 21');
    }

    public function test_no_crea_una_mesa_con_tipo_grupo(): void
    {
        $this->autenticar();

        $this->from(route('mesas'))->post(route('mesas.guardar'), [
            'formulario' => 'mesa',
            'numero_mesa' => 21,
            'tipo' => TipoMesa::Grupo->value,
        ])->assertRedirect(route('mesas'))
            ->assertSessionHasErrors('tipo');
    }

    public function test_actualiza_una_mesa(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(4, TipoMesa::Mesa);

        $this->put(route('mesas.actualizar', $mesa->id_mesa), [
            'formulario' => 'mesa',
            'numero_mesa' => 8,
            'tipo' => TipoMesa::Barra->value,
        ])->assertRedirect(route('mesas'));

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Mesa 8')
            ->assertSee('Barra')
            ->assertDontSee('Mesa 4');
    }

    public function test_elimina_una_mesa_libre(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(9, TipoMesa::Mesa);

        $this->delete(route('mesas.eliminar', $mesa->id_mesa))
            ->assertRedirect(route('mesas'));

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('No hay mesas en el catálogo.');
    }

    public function test_no_elimina_una_mesa_con_pedidos(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(6, TipoMesa::Mesa);
        $this->crearPedidoActivo($mesa, 'Brownie', 1, '15.00');

        $this->delete(route('mesas.eliminar', $mesa->id_mesa))
            ->assertRedirect(route('mesas'))
            ->assertSessionHas('error', 'No se puede eliminar la mesa porque tiene pedidos asociados.');

        $this->assertDatabaseHas('mesa', ['id_mesa' => $mesa->id_mesa]);
    }

    public function test_une_mesas_y_las_muestra_en_grupo(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Unir mesas');

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $this->get(route('mesas', ['tipo' => 'grupo']))
            ->assertOk()
            ->assertSee('Mesas 1 y 2')
            ->assertSee('Grupo')
            ->assertSee('aria-label="Ver Mesas 1 y 2"', false)
            ->assertSee('aria-label="Separar Mesas 1 y 2"', false);

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Liberar mesas');

        $this->get(route('mesas', ['tipo' => 'mesa']))
            ->assertOk()
            ->assertSee('No hay mesas en el catálogo.')
            ->assertDontSee('Mesas 1 y 2');
    }

    public function test_no_une_una_barra(): void
    {
        $this->autenticar();
        $barra = $this->crearMesa(1, TipoMesa::Barra);
        $mesa = $this->crearMesa(2, TipoMesa::Mesa);

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$barra->id_mesa, $mesa->id_mesa],
        ])->assertRedirect(route('mesas'))
            ->assertSessionHas('error', 'Solo se pueden unir mesas. La barra no forma grupos.');
    }

    public function test_separa_un_grupo_sin_borrar_las_mesas(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $grupoId = (int) $primera->fresh()->id_grupo;

        $this->delete(route('mesas.grupos.eliminar', $grupoId))
            ->assertRedirect(route('mesas'));

        $this->get(route('mesas', ['tipo' => 'mesa']))
            ->assertOk()
            ->assertSee('Mesa 1')
            ->assertSee('Mesa 2');

        $this->get(route('mesas', ['tipo' => 'grupo']))
            ->assertOk()
            ->assertSee('No hay mesas unidas.');
    }

    public function test_libera_las_mesas_unidas_desde_el_modal(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $grupoId = (int) $primera->fresh()->id_grupo;

        $this->get(route('mesas', ['liberar' => 1]))
            ->assertOk()
            ->assertSee('Liberar mesas')
            ->assertSee('Mesas 1 y 2')
            ->assertSee('data-abrir', false);

        $this->post(route('mesas.grupos.liberar'), [
            'formulario' => 'liberar',
            'grupos' => [$grupoId],
        ])->assertRedirect(route('mesas'));

        $this->get(route('mesas', ['tipo' => 'mesa']))
            ->assertOk()
            ->assertSee('Mesa 1')
            ->assertSee('Mesa 2')
            ->assertDontSee('Liberar mesas');
    }

    public function test_no_elimina_una_mesa_si_esta_en_un_grupo(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $this->delete(route('mesas.eliminar', $primera->id_mesa))
            ->assertRedirect(route('mesas'))
            ->assertSessionHas('error', 'Separa el grupo antes de eliminar o cambiar esa mesa.');
    }

    public function test_el_pedido_del_grupo_se_abre_en_el_modal(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($primera, 'Limonada', 1, '8.50');

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $grupoId = (int) $primera->fresh()->id_grupo;

        $this->get(route('mesas', ['pedido_grupo' => $grupoId]))
            ->assertOk()
            ->assertSee('Pedido de Mesas 1 y 2')
            ->assertSee('Limonada')
            ->assertSee('Terminar pedido')
            ->assertSee('data-abrir', false);
    }

    public function test_solo_puede_haber_un_pedido_activo_por_mesa(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(4, TipoMesa::Mesa);
        $producto = $this->crearProducto();
        $gestion = $this->app->make(GestionPedidosServiceInterface::class);
        $datos = new GuardarPedidoMesaDatos((int) $mesa->id_mesa, [
            ['id_producto' => (int) $producto->id_producto, 'cantidad' => 1],
        ]);

        $gestion->crearActivo($datos);

        $this->expectException(PedidoActivoYaExisteException::class);

        $gestion->crearActivo($datos);
    }

    public function test_un_pedido_cerrado_no_aparece_como_activo(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(5, TipoMesa::Barra);
        Pedido::query()->create([
            'fecha' => now(),
            'estado' => EstadoPedido::Cerrado->value,
            'total' => 10,
            'id_mesa' => $mesa->id_mesa,
        ]);

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Sin pedido')
            ->assertSee('$ 0,00')
            ->assertDontSee('aria-label="Ver pedido de Mesa 5"', false);
    }

    public function test_termina_el_pedido_activo_y_lo_registra_en_historial(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(3, TipoMesa::Mesa);
        $this->crearPedidoActivo($mesa, 'Limonada', 2, '8.50');

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Terminar pedido')
            ->assertSee('aria-label="Terminar pedido de Mesa 3"', false);

        $this->post(route('mesas.pedido.terminar', $mesa->id_mesa))
            ->assertRedirect(route('mesas'))
            ->assertSessionHas('exito', 'Pedido terminado correctamente.');

        $this->assertDatabaseHas('pedido', [
            'id_mesa' => $mesa->id_mesa,
            'estado' => EstadoPedido::Cerrado->value,
            'total' => '17.00',
        ]);
        $this->assertDatabaseHas('historial', [
            'accion' => AccionHistorial::TerminarPedido->value,
            'id_usuario' => auth()->id(),
            'descripcion' => 'Pedido de Mesa 3 cerrado. Total $ 17,00.',
        ]);

        $this->get(route('mesas'))
            ->assertOk()
            ->assertSee('Sin pedido')
            ->assertDontSee('aria-label="Terminar pedido de Mesa 3"', false);
    }

    public function test_no_termina_si_la_mesa_no_tiene_pedido_activo(): void
    {
        $this->autenticar();
        $mesa = $this->crearMesa(3, TipoMesa::Mesa);

        $this->post(route('mesas.pedido.terminar', $mesa->id_mesa))
            ->assertRedirect(route('mesas'))
            ->assertSessionHas('error', 'La mesa no tiene un pedido activo para terminar.');
    }

    public function test_termina_el_pedido_activo_de_un_grupo(): void
    {
        $this->autenticar();
        $primera = $this->crearMesa(1, TipoMesa::Mesa);
        $segunda = $this->crearMesa(2, TipoMesa::Mesa);
        $this->crearPedidoActivo($primera, 'Limonada', 1, '8.50');

        $this->post(route('mesas.grupos.guardar'), [
            'formulario' => 'grupo',
            'mesas' => [$primera->id_mesa, $segunda->id_mesa],
        ])->assertRedirect(route('mesas'));

        $grupoId = (int) $primera->fresh()->id_grupo;

        $this->post(route('mesas.grupos.pedido.terminar', $grupoId))
            ->assertRedirect(route('mesas'))
            ->assertSessionHas('exito', 'Pedido terminado correctamente.');

        $this->assertDatabaseHas('pedido', [
            'id_mesa' => $primera->id_mesa,
            'estado' => EstadoPedido::Cerrado->value,
        ]);
        $this->assertDatabaseHas('historial', [
            'accion' => AccionHistorial::TerminarPedido->value,
            'descripcion' => 'Pedido de Mesa 1 cerrado. Total $ 8,50.',
        ]);
    }

    private function crearMesa(int $numero, TipoMesa $tipo): Mesa
    {
        $codigo = CodigoQr::query()->create([
            'numero_qr' => $numero,
            'estado' => 'activo',
            'codigo_qr' => 'OLIMPIA-MESA-'.str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
        ]);

        return Mesa::query()->create([
            'numero_mesa' => $numero,
            'tipo' => $tipo->value,
            'estado' => 'disponible',
            'id_qr' => $codigo->id_qr,
        ]);
    }

    private function crearPedidoActivo(Mesa $mesa, string $nombre, int $cantidad, string $precio): Pedido
    {
        $producto = $this->crearProducto(['nombre' => $nombre, 'precio' => $precio]);
        $pedido = Pedido::query()->create([
            'fecha' => now(),
            'estado' => EstadoPedido::Activo->value,
            'total' => (float) $precio * $cantidad,
            'id_mesa' => $mesa->id_mesa,
        ]);
        $pedido->detalles()->create([
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => (float) $precio * $cantidad,
            'id_producto' => $producto->id_producto,
        ]);

        return $pedido;
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function crearProducto(array $extra = []): Producto
    {
        $categoria = Categoria::query()->firstWhere('nombre', 'Bebidas')
            ?? Categoria::query()->create([
                'nombre' => 'Bebidas',
                'descripcion' => 'Categoría de menú',
            ]);

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
}
