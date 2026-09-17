<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\DTOs\Dashboard\FilaCatalogoMesasDatos;
use App\DTOs\Dashboard\FiltroMesasDatos;
use App\Enums\EstadoPedido;
use App\Enums\TipoMesa;
use App\Models\CodigoQr;
use App\Models\DetallePedido;
use App\Models\GrupoMesa;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\CatalogoMesasService;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CatalogoMesasServiceTest extends TestCase
{
    private MesaRepositoryInterface&MockInterface $mesas;

    private GrupoMesaRepositoryInterface&MockInterface $grupos;

    private CatalogoMesasService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mesas = Mockery::mock(MesaRepositoryInterface::class);
        $this->mesas->shouldReceive('siguienteNumero')->andReturn(1)->byDefault();
        $this->grupos = Mockery::mock(GrupoMesaRepositoryInterface::class);
        $this->service = new CatalogoMesasService($this->mesas, $this->grupos);
    }

    public function test_catalogo_vacio_si_no_hay_mesas(): void
    {
        $this->grupos->shouldReceive('catalogo')->once()->andReturn(collect());
        $this->mesas->shouldReceive('catalogoSinGrupo')->once()->with(null)->andReturn(collect());
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertFalse($catalogo->tieneMesas());
        $this->assertCount(4, $catalogo->tipos);
        $this->assertCount(2, $catalogo->tiposPersistibles);
        $this->assertFalse($catalogo->puedeUnir());
        $this->assertFalse($catalogo->puedeLiberar());
        $this->assertSame(1, $catalogo->siguienteNumero);
    }

    public function test_propone_el_siguiente_numero_de_mesa(): void
    {
        $this->grupos->shouldReceive('catalogo')->once()->andReturn(collect());
        $this->mesas->shouldReceive('catalogoSinGrupo')->once()->with(null)->andReturn(collect());
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());
        $this->mesas->shouldReceive('siguienteNumero')->once()->andReturn(13);

        $catalogo = $this->service->obtenerCatalogo();

        $this->assertSame(13, $catalogo->siguienteNumero);
    }

    public function test_convierte_las_mesas_en_filas_con_pedido_y_cuenta(): void
    {
        $libre = $this->mesa(1, TipoMesa::Barra);
        $ocupada = $this->mesa(2, TipoMesa::Mesa, true);
        $filtro = FiltroMesasDatos::predeterminado();

        $this->grupos->shouldReceive('catalogo')->once()->andReturn(collect());
        $this->mesas->shouldReceive('catalogoSinGrupo')->once()->with(null)->andReturn(Collection::make([$libre, $ocupada]));
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $tarjeta = $catalogo->enOrden()[1];

        $this->assertTrue($catalogo->tieneMesas());
        $this->assertInstanceOf(FilaCatalogoMesasDatos::class, $tarjeta);
        $this->assertTrue($tarjeta->ocupada);
        $this->assertSame('Limonada', $tarjeta->resumenPedido());
        $this->assertSame('$ 8,50', $tarjeta->cuentaFormateada());
        $this->assertSame('Sin pedido', $catalogo->enOrden()[0]->resumenPedido());
        $this->assertSame('$ 0,00', $catalogo->enOrden()[0]->cuentaFormateada());
    }

    public function test_filtra_por_tipo_al_pedir_el_catalogo(): void
    {
        $filtro = FiltroMesasDatos::fromInput('barra');
        $this->grupos->shouldReceive('catalogo')->once()->andReturn(collect());
        $this->mesas->shouldReceive('catalogoSinGrupo')->once()->with(TipoMesa::Barra)->andReturn(collect());
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());

        $this->service->obtenerCatalogo($filtro);
    }

    public function test_el_filtro_de_grupo_lista_las_uniones(): void
    {
        $filtro = FiltroMesasDatos::fromInput('grupo');
        $grupo = new GrupoMesa(['estado' => 'activo']);
        $grupo->id_grupo = 4;
        $grupo->setRelation('mesas', Collection::make([
            $this->mesa(1, TipoMesa::Mesa),
            $this->mesa(2, TipoMesa::Mesa),
        ]));

        $this->grupos->shouldReceive('catalogo')->once()->andReturn(Collection::make([$grupo]));
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $fila = $catalogo->enOrden()[0];

        $this->assertTrue($fila->esGrupo);
        $this->assertSame('Mesas 1 y 2', $fila->etiqueta);
        $this->assertSame('Grupo', $fila->subtitulo);
        $this->assertTrue($catalogo->puedeLiberar());
        $this->assertCount(1, $catalogo->grupos);
    }

    public function test_el_filtro_de_pedidos_activos_lista_mesas_y_grupos_ocupados(): void
    {
        $filtro = FiltroMesasDatos::fromInput('pedidos-activos');
        $grupoLibre = new GrupoMesa(['estado' => 'activo']);
        $grupoLibre->id_grupo = 4;
        $grupoLibre->setRelation('mesas', Collection::make([
            $this->mesa(5, TipoMesa::Mesa),
            $this->mesa(6, TipoMesa::Mesa),
        ]));
        $grupoOcupado = new GrupoMesa(['estado' => 'activo']);
        $grupoOcupado->id_grupo = 8;
        $grupoOcupado->setRelation('mesas', Collection::make([
            $this->mesa(7, TipoMesa::Mesa, true),
            $this->mesa(8, TipoMesa::Mesa),
        ]));

        $this->grupos->shouldReceive('catalogo')->once()->andReturn(Collection::make([$grupoLibre, $grupoOcupado]));
        $this->mesas->shouldReceive('catalogoSinGrupo')->once()->with(null)->andReturn(Collection::make([
            $this->mesa(1, TipoMesa::Barra),
            $this->mesa(3, TipoMesa::Mesa, true),
        ]));
        $this->mesas->shouldReceive('disponiblesParaUnir')->once()->andReturn(collect());

        $catalogo = $this->service->obtenerCatalogo($filtro);
        $filas = $catalogo->enOrden();

        $this->assertCount(2, $filas);
        $this->assertSame('Mesa 3', $filas[0]->etiqueta);
        $this->assertSame('Mesas 7 y 8', $filas[1]->etiqueta);
        $this->assertTrue($filas[0]->ocupada);
        $this->assertTrue($filas[1]->ocupada);
        $this->assertTrue($filas[1]->esGrupo);
    }

    private function mesa(int $id, TipoMesa $tipo, bool $ocupada = false): Mesa
    {
        $mesa = new Mesa([
            'numero_mesa' => $id,
            'tipo' => $tipo,
        ]);
        $mesa->id_mesa = $id;
        $mesa->setRelation('codigoQr', new CodigoQr([
            'codigo_qr' => 'OLIMPIA-MESA-'.str_pad((string) $id, 2, '0', STR_PAD_LEFT),
        ]));
        $mesa->setRelation('pedidoActivo', $ocupada ? $this->pedido() : null);

        return $mesa;
    }

    private function pedido(): Pedido
    {
        $detalle = new DetallePedido([
            'cantidad' => 1,
            'precio_unitario' => '8.50',
            'subtotal' => '8.50',
        ]);
        $detalle->setRelation('producto', new Producto(['nombre' => 'Limonada']));

        $pedido = new Pedido([
            'estado' => EstadoPedido::Activo,
            'total' => '8.50',
        ]);
        $pedido->id_pedido = 11;
        $pedido->setRelation('detalles', collect([$detalle]));

        return $pedido;
    }
}
