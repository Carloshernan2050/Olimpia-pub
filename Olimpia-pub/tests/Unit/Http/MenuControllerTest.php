<?php

namespace Tests\Unit\Http;

use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\DTOs\Dashboard\CatalogoMenuDatos;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Requests\ConsultarCatalogoMenuRequest;
use Mockery;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    public function test_mostrar_envia_el_catalogo_a_la_vista(): void
    {
        $catalogo = new CatalogoMenuDatos([], []);
        $catalogoServicio = Mockery::mock(CatalogoMenuServiceInterface::class);
        $catalogoServicio->shouldReceive('obtenerCatalogo')
            ->once()
            ->with(Mockery::type(FiltroMenuDatos::class))
            ->andReturn($catalogo);

        $request = ConsultarCatalogoMenuRequest::create('/dashboard/menu', 'GET');
        $controlador = new MenuController($catalogoServicio);
        $vista = $controlador->mostrar($request);

        $this->assertSame('dashboard.menu', $vista->name());
        $this->assertSame($catalogo, $vista['catalogo']);
        $this->assertInstanceOf(FiltroMenuDatos::class, $vista['filtro']);
    }
}
