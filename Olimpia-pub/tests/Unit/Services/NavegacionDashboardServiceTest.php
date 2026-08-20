<?php

namespace Tests\Unit\Services;

use App\Services\NavegacionDashboardService;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class NavegacionDashboardServiceTest extends TestCase
{
    public function test_home_es_el_unico_item_con_ruta(): void
    {
        $service = new NavegacionDashboardService(Request::create('/dashboard'));
        $items = $service->items();
        $inicio = $items[0];

        $this->assertCount(9, $items);
        $this->assertSame('inicio', $inicio->clave);
        $this->assertSame('dashboard', $inicio->ruta);
        $this->assertTrue($inicio->estaDisponible());
        $this->assertCount(1, array_filter($items, fn ($item) => $item->estaDisponible()));
    }

    public function test_la_cabecera_incluye_perfil(): void
    {
        $service = new NavegacionDashboardService(Request::create('/dashboard'));
        $acciones = $service->accionesCabecera();
        $perfil = end($acciones);

        $this->assertCount(5, $acciones);
        $this->assertSame('perfil', $perfil->clave);
        $this->assertTrue($perfil->esPerfil);
    }

    public function test_seccion_activa_es_inicio_en_la_ruta_dashboard(): void
    {
        $request = Request::create('/dashboard');
        $ruta = new Route(['GET'], '/dashboard', fn () => null);
        $ruta->name('dashboard');
        $request->setRouteResolver(fn () => $ruta);

        $service = new NavegacionDashboardService($request);

        $this->assertSame('inicio', $service->seccionActiva());
    }

    public function test_seccion_activa_queda_vacia_fuera_del_dashboard(): void
    {
        $service = new NavegacionDashboardService(Request::create('/otra'));

        $this->assertSame('', $service->seccionActiva());
    }
}
