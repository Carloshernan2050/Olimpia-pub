<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Contracts\Services\EnlacePedidoMesaInterface;
use App\Contracts\Services\GeneradorCodigoQrInterface;
use App\DTOs\Dashboard\GuardarMesaDatos;
use App\Enums\TipoMesa;
use App\Exceptions\Mesa\MesaConPedidosException;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Exceptions\Mesa\MesaNumeroDuplicadoException;
use App\Models\CodigoQr;
use App\Models\Mesa;
use App\Services\GestionMesasService;
use Illuminate\Database\ConnectionInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionMesasServiceTest extends TestCase
{
    private MesaRepositoryInterface&MockInterface $mesas;

    private CodigoQrRepositoryInterface&MockInterface $codigos;

    private PedidoRepositoryInterface&MockInterface $pedidos;

    private GeneradorCodigoQrInterface&MockInterface $qr;

    private EnlacePedidoMesaInterface&MockInterface $enlace;

    private ConnectionInterface&MockInterface $conexion;

    private GestionMesasService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mesas = Mockery::mock(MesaRepositoryInterface::class);
        $this->codigos = Mockery::mock(CodigoQrRepositoryInterface::class);
        $this->pedidos = Mockery::mock(PedidoRepositoryInterface::class);
        $this->qr = Mockery::mock(GeneradorCodigoQrInterface::class);
        $this->enlace = Mockery::mock(EnlacePedidoMesaInterface::class);
        $this->conexion = Mockery::mock(ConnectionInterface::class);
        $this->conexion->shouldReceive('transaction')->andReturnUsing(fn (callable $accion) => $accion());
        $this->service = new GestionMesasService(
            $this->mesas,
            $this->codigos,
            $this->pedidos,
            $this->qr,
            $this->enlace,
            $this->conexion,
        );
    }

    public function test_falla_si_el_numero_ya_existe(): void
    {
        $this->mesas->shouldReceive('findByNumero')->once()->with(4)->andReturn(new Mesa);

        $this->expectException(MesaNumeroDuplicadoException::class);

        $this->service->crear(new GuardarMesaDatos(4, TipoMesa::Barra));
    }

    public function test_crea_la_mesa_con_su_codigo_qr(): void
    {
        $codigo = new CodigoQr(['codigo_qr' => 'OLIMPIA-MESA-13']);
        $codigo->id_qr = 8;
        $mesa = new Mesa(['numero_mesa' => 13, 'tipo' => TipoMesa::Mesa]);
        $mesa->id_mesa = 13;
        $mesa->setRelation('codigoQr', $codigo);
        $mesa->setRelation('pedidoActivo', null);

        $this->mesas->shouldReceive('findByNumero')->once()->with(13)->andReturn(null);
        $this->codigos->shouldReceive('findByNumero')->once()->with(13)->andReturn(null);
        $this->codigos->shouldReceive('create')->once()->andReturn($codigo);
        $this->mesas->shouldReceive('create')->once()->andReturn($mesa);
        $this->enlace->shouldReceive('url')->once()->with('OLIMPIA-MESA-13')->andReturn('http://localhost/mesa/OLIMPIA-MESA-13');
        $this->qr->shouldReceive('svg')->once()->with('http://localhost/mesa/OLIMPIA-MESA-13')->andReturn('<svg></svg>');

        $creada = $this->service->crear(new GuardarMesaDatos(13, TipoMesa::Mesa));

        $this->assertSame(13, $creada->numero);
        $this->assertSame('<svg></svg>', $creada->qrSvg);
        $this->assertSame('Sin pedido', $creada->resumenPedido());
    }

    public function test_no_elimina_una_mesa_con_pedidos(): void
    {
        $mesa = new Mesa(['numero_mesa' => 2]);
        $mesa->id_mesa = 2;
        $this->mesas->shouldReceive('findById')->once()->with(2)->andReturn($mesa);
        $this->pedidos->shouldReceive('existenDeMesa')->once()->with(2)->andReturn(true);

        $this->expectException(MesaConPedidosException::class);

        $this->service->eliminar(2);
    }

    public function test_eliminar_falla_si_la_mesa_no_existe(): void
    {
        $this->mesas->shouldReceive('findById')->once()->with(9)->andReturn(null);

        $this->expectException(MesaNoEncontradaException::class);

        $this->service->eliminar(9);
    }

    public function test_elimina_la_mesa_y_su_codigo(): void
    {
        $codigo = new CodigoQr(['codigo_qr' => 'OLIMPIA-MESA-02']);
        $mesa = new Mesa(['numero_mesa' => 2]);
        $mesa->id_mesa = 2;
        $mesa->setRelation('codigoQr', $codigo);

        $this->mesas->shouldReceive('findById')->once()->with(2)->andReturn($mesa);
        $this->pedidos->shouldReceive('existenDeMesa')->once()->with(2)->andReturn(false);
        $this->mesas->shouldReceive('delete')->once()->with($mesa);
        $this->codigos->shouldReceive('delete')->once()->with($codigo);

        $this->service->eliminar(2);
    }
}
