<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\CatalogoInventarioServiceInterface;
use App\Contracts\Services\GestionInventarioServiceInterface;
use App\DTOs\Dashboard\CatalogoInventarioDatos;
use App\DTOs\Dashboard\FiltroInventarioDatos;
use App\DTOs\Dashboard\PaginacionInventarioDatos;
use App\DTOs\Dashboard\ResumenInventarioDatos;
use App\Http\Controllers\Dashboard\InventarioController;
use App\Http\Requests\ConsultarInventarioRequest;
use Mockery;
use Tests\TestCase;

class InventarioControllerTest extends TestCase
{
    public function test_mostrar_envia_el_catalogo_a_la_vista(): void
    {
        $catalogo = new CatalogoInventarioDatos(
            [],
            new ResumenInventarioDatos(0, 0, 0, 0),
            new PaginacionInventarioDatos(1, 1, null, null),
            [],
            [],
        );
        $catalogoServicio = Mockery::mock(CatalogoInventarioServiceInterface::class);
        $catalogoServicio->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroInventarioDatos::class))
            ->andReturn($catalogo);

        $gestion = Mockery::mock(GestionInventarioServiceInterface::class);
        $gestion->shouldReceive('listar')->once()->andReturn([]);

        $request = ConsultarInventarioRequest::create('/dashboard/inventario', 'GET');
        $controlador = new InventarioController($catalogoServicio, $gestion);
        $vista = $controlador->mostrar($request);

        $this->assertSame('dashboard.inventario', $vista->name());
        $this->assertSame($catalogo, $vista['catalogo']);
        $this->assertFalse($vista['abrirModal']);
        $this->assertFalse($vista['formularioMovimiento']);
        $this->assertNull($vista['movimientoEditar']);
        $this->assertNull($vista['productoVer']);
    }
}
