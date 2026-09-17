<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\MenuPedidoMesaServiceInterface;
use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Pedido\MesaPedidoPublicoDatos;
use App\Http\Controllers\MesaPedidoController;
use App\Http\Requests\ConsultarMenuPedidoRequest;
use Mockery;
use Tests\TestCase;

class MesaPedidoControllerTest extends TestCase
{
    public function test_mostrar_envia_la_mesa_y_el_filtro_a_la_vista(): void
    {
        $mesa = new MesaPedidoPublicoDatos(
            3,
            3,
            'OLIMPIA-MESA-03',
            new CatalogoMenuDatos([], []),
            null,
        );
        $servicio = Mockery::mock(MenuPedidoMesaServiceInterface::class);
        $servicio->shouldReceive('obtener')
            ->once()
            ->with('OLIMPIA-MESA-03', Mockery::type(FiltroMenuDatos::class))
            ->andReturn($mesa);

        $request = ConsultarMenuPedidoRequest::create('/mesa/OLIMPIA-MESA-03', 'GET');
        $controlador = new MesaPedidoController($servicio);
        $vista = $controlador->mostrar($request, 'OLIMPIA-MESA-03');

        $this->assertSame('pedido.menu', $vista->name());
        $this->assertSame($mesa, $vista['mesa']);
        $this->assertInstanceOf(FiltroMenuDatos::class, $vista['filtro']);
    }
}
