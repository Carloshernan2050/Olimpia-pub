<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Models\CodigoQr;
use App\Models\Mesa;
use App\Services\MenuPedidoMesaService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class MenuPedidoMesaServiceTest extends TestCase
{
    private MesaRepositoryInterface&MockInterface $mesas;

    private CatalogoMenuServiceInterface&MockInterface $catalogo;

    private GestionPedidosServiceInterface&MockInterface $pedidos;

    private MenuPedidoMesaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mesas = Mockery::mock(MesaRepositoryInterface::class);
        $this->catalogo = Mockery::mock(CatalogoMenuServiceInterface::class);
        $this->pedidos = Mockery::mock(GestionPedidosServiceInterface::class);
        $this->service = new MenuPedidoMesaService($this->mesas, $this->catalogo, $this->pedidos);
    }

    public function test_obtener_falla_si_el_codigo_no_existe(): void
    {
        $this->mesas->shouldReceive('findByCodigoQr')->once()->with('OLIMPIA-MESA-99')->andReturn(null);

        $this->expectException(MesaNoEncontradaException::class);

        $this->service->obtener('OLIMPIA-MESA-99');
    }

    public function test_obtener_devuelve_la_mesa_y_el_catalogo(): void
    {
        $catalogo = new CatalogoMenuDatos([], []);
        $this->mesas->shouldReceive('findByCodigoQr')->once()->with('OLIMPIA-MESA-03')->andReturn($this->mesa());
        $this->catalogo->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroMenuDatos::class))
            ->andReturn($catalogo);

        $datos = $this->service->obtener('OLIMPIA-MESA-03', FiltroMenuDatos::predeterminado());

        $this->assertSame(3, $datos->id);
        $this->assertSame('Mesa 3', $datos->etiqueta());
        $this->assertSame('OLIMPIA-MESA-03', $datos->codigo);
        $this->assertSame($catalogo, $datos->catalogo);
        $this->assertNull($datos->pedidoActivo);
    }

    public function test_pedir_registra_el_pedido_en_la_mesa_del_qr(): void
    {
        $pedido = new PedidoMesaDatos(21, '8.50', []);
        $this->mesas->shouldReceive('findByCodigoQr')->once()->with('OLIMPIA-MESA-03')->andReturn($this->mesa());
        $this->pedidos->shouldReceive('registrar')
            ->once()
            ->with(Mockery::on(function (GuardarPedidoMesaDatos $datos): bool {
                return $datos->idMesa === 3
                    && $datos->lineas === [['id_producto' => 7, 'cantidad' => 1]];
            }))
            ->andReturn($pedido);

        $creado = $this->service->pedir('OLIMPIA-MESA-03', new GuardarPedidoMesaDatos(0, [
            ['id_producto' => 7, 'cantidad' => 1],
        ]));

        $this->assertSame(21, $creado->id);
    }

    private function mesa(): Mesa
    {
        $codigo = new CodigoQr(['codigo_qr' => 'OLIMPIA-MESA-03', 'estado' => 'activo']);
        $mesa = new Mesa(['numero_mesa' => 3]);
        $mesa->id_mesa = 3;
        $mesa->setRelation('codigoQr', $codigo);
        $mesa->setRelation('pedidoActivo', null);

        return $mesa;
    }
}
