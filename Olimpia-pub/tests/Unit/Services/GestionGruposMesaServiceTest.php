<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Services\EnlacePedidoMesaInterface;
use App\Contracts\Services\GeneradorCodigoQrInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\DTOs\Dashboard\GuardarGrupoMesaDatos;
use App\DTOs\Dashboard\GuardarLiberarGruposDatos;
use App\Enums\TipoMesa;
use App\Exceptions\Mesa\GrupoInsuficienteException;
use App\Exceptions\Mesa\GrupoNoEncontradoException;
use App\Exceptions\Mesa\MesaNoUnibleException;
use App\Exceptions\Mesa\PedidoActivoNoEncontradoException;
use App\Models\GrupoMesa;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Services\GestionGruposMesaService;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GestionGruposMesaServiceTest extends TestCase
{
    private GrupoMesaRepositoryInterface&MockInterface $grupos;

    private MesaRepositoryInterface&MockInterface $mesas;

    private GeneradorCodigoQrInterface&MockInterface $qr;

    private EnlacePedidoMesaInterface&MockInterface $enlace;

    private GestionPedidosServiceInterface&MockInterface $pedidos;

    private ConnectionInterface&MockInterface $conexion;

    private GestionGruposMesaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grupos = Mockery::mock(GrupoMesaRepositoryInterface::class);
        $this->mesas = Mockery::mock(MesaRepositoryInterface::class);
        $this->qr = Mockery::mock(GeneradorCodigoQrInterface::class);
        $this->enlace = Mockery::mock(EnlacePedidoMesaInterface::class);
        $this->enlace->shouldReceive('url')->andReturnUsing(
            fn (string $codigo): string => 'http://localhost/mesa/'.$codigo,
        );
        $this->pedidos = Mockery::mock(GestionPedidosServiceInterface::class);
        $this->conexion = Mockery::mock(ConnectionInterface::class);
        $this->conexion->shouldReceive('transaction')->andReturnUsing(fn (callable $accion) => $accion());
        $this->service = new GestionGruposMesaService(
            $this->grupos,
            $this->mesas,
            $this->qr,
            $this->enlace,
            $this->pedidos,
            $this->conexion,
        );
    }

    public function test_exige_al_menos_dos_mesas(): void
    {
        $this->expectException(GrupoInsuficienteException::class);

        $this->service->unir(new GuardarGrupoMesaDatos([1]));
    }

    public function test_no_une_una_barra(): void
    {
        $this->mesas->shouldReceive('findByIds')->once()->with([1, 2])->andReturn(Collection::make([
            $this->mesa(1, TipoMesa::Barra),
            $this->mesa(2, TipoMesa::Mesa),
        ]));

        $this->expectException(MesaNoUnibleException::class);

        $this->service->unir(new GuardarGrupoMesaDatos([1, 2]));
    }

    public function test_une_dos_mesas_en_un_grupo(): void
    {
        $primera = $this->mesa(1, TipoMesa::Mesa);
        $segunda = $this->mesa(2, TipoMesa::Mesa);
        $grupo = new GrupoMesa(['estado' => 'activo']);
        $grupo->id_grupo = 9;
        $grupo->setRelation('mesas', Collection::make([$primera, $segunda]));

        $this->mesas->shouldReceive('findByIds')->once()->with([1, 2])->andReturn(Collection::make([$primera, $segunda]));
        $this->grupos->shouldReceive('create')->once()->with(['estado' => 'activo'])->andReturn($grupo);
        $this->mesas->shouldReceive('asignarGrupo')->once()->with([1, 2], 9);
        $this->grupos->shouldReceive('findById')->once()->with(9)->andReturn($grupo);
        $this->qr->shouldReceive('svg')->twice()->andReturn('<svg></svg>');

        $creado = $this->service->unir(new GuardarGrupoMesaDatos([1, 2]));

        $this->assertSame(9, $creado->id);
        $this->assertSame('Mesas 1 y 2', $creado->etiqueta());
        $this->assertCount(2, $creado->mesas);
    }

    public function test_separar_falla_si_el_grupo_no_existe(): void
    {
        $this->grupos->shouldReceive('findById')->once()->with(4)->andReturn(null);

        $this->expectException(GrupoNoEncontradoException::class);

        $this->service->separar(4);
    }

    public function test_libera_los_grupos_seleccionados(): void
    {
        $primera = $this->mesa(1, TipoMesa::Mesa);
        $segunda = $this->mesa(2, TipoMesa::Mesa);
        $grupo = new GrupoMesa(['estado' => 'activo']);
        $grupo->id_grupo = 3;
        $grupo->setRelation('mesas', Collection::make([$primera, $segunda]));

        $this->grupos->shouldReceive('findById')->once()->with(3)->andReturn($grupo);
        $this->mesas->shouldReceive('asignarGrupo')->once()->with([1, 2], null);
        $this->grupos->shouldReceive('delete')->once()->with($grupo);

        $this->service->liberar(new GuardarLiberarGruposDatos([3]));
    }

    public function test_terminar_pedido_falla_si_el_grupo_esta_libre(): void
    {
        $grupo = new GrupoMesa(['estado' => 'activo']);
        $grupo->id_grupo = 3;
        $grupo->setRelation('mesas', Collection::make([
            $this->mesa(1, TipoMesa::Mesa),
            $this->mesa(2, TipoMesa::Mesa),
        ]));
        $this->grupos->shouldReceive('findById')->once()->with(3)->andReturn($grupo);

        $this->expectException(PedidoActivoNoEncontradoException::class);

        $this->service->terminarPedido(3, 4);
    }

    public function test_terminar_pedido_cierra_el_activo_del_grupo(): void
    {
        $ocupada = $this->mesa(2, TipoMesa::Mesa);
        $ocupada->setRelation('pedidoActivo', new Pedido);
        $grupo = new GrupoMesa(['estado' => 'activo']);
        $grupo->id_grupo = 3;
        $grupo->setRelation('mesas', Collection::make([
            $this->mesa(1, TipoMesa::Mesa),
            $ocupada,
        ]));
        $this->grupos->shouldReceive('findById')->once()->with(3)->andReturn($grupo);
        $this->pedidos->shouldReceive('terminar')->once()->with(2, 4);

        $this->service->terminarPedido(3, 4);
    }

    private function mesa(int $id, TipoMesa $tipo): Mesa
    {
        $mesa = new Mesa([
            'numero_mesa' => $id,
            'tipo' => $tipo,
        ]);
        $mesa->id_mesa = $id;
        $mesa->setRelation('codigoQr', null);
        $mesa->setRelation('pedidoActivo', null);

        return $mesa;
    }
}
