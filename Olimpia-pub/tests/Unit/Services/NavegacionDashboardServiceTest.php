<?php

namespace Tests\Unit\Services;

use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Models\Usuario;
use App\Services\NavegacionDashboardService;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Mockery;
use Tests\TestCase;

class NavegacionDashboardServiceTest extends TestCase
{
    public function test_home_promociones_e_inventario_tienen_ruta(): void
    {
        $service = $this->servicio(Request::create('/dashboard'), true);
        $items = $service->items();
        $inicio = $items[0];
        $promociones = $items[1];
        $inventario = $items[4];

        $this->assertCount(9, $items);
        $this->assertSame('inicio', $inicio->clave);
        $this->assertSame('dashboard', $inicio->ruta);
        $this->assertTrue($inicio->estaDisponible());
        $this->assertSame('promociones', $promociones->clave);
        $this->assertSame('etiqueta', $promociones->icono);
        $this->assertSame('promociones', $promociones->ruta);
        $this->assertTrue($promociones->estaDisponible());
        $this->assertSame('inventario', $inventario->clave);
        $this->assertSame('inventario', $inventario->ruta);
        $this->assertSame('portapapeles', $inventario->icono);
        $this->assertSame('carta', $items[3]->clave);
        $this->assertSame('comida', $items[3]->icono);
        $this->assertSame('menu', $items[3]->ruta);
        $this->assertTrue($items[3]->estaDisponible());
        $this->assertSame('mesa', $items[5]->icono);
        $this->assertTrue($inventario->estaDisponible());
        $this->assertSame('eventos', $items[2]->clave);
        $this->assertSame('megafono', $items[2]->icono);
        $this->assertSame('eventos', $items[2]->ruta);
        $this->assertTrue($items[2]->estaDisponible());
        $this->assertSame('mesas', $items[5]->clave);
        $this->assertCount(5, array_filter($items, fn ($item) => $item->estaDisponible()));
    }

    public function test_sin_permiso_el_inventario_queda_inactivo(): void
    {
        $items = $this->servicio(Request::create('/dashboard'), false)->items();
        $inventario = $items[4];

        $this->assertCount(9, $items);
        $this->assertSame('inventario', $inventario->clave);
        $this->assertFalse($inventario->estaDisponible());
        $this->assertTrue($items[3]->estaDisponible());
        $this->assertCount(4, array_filter($items, fn ($item) => $item->estaDisponible()));
    }

    public function test_la_cabecera_incluye_perfil(): void
    {
        $service = $this->servicio(Request::create('/dashboard'));
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

        $this->assertSame('inicio', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_es_promociones_en_su_ruta(): void
    {
        $request = Request::create('/dashboard/promociones');
        $ruta = new Route(['GET'], '/dashboard/promociones', fn () => null);
        $ruta->name('promociones');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('promociones', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_es_carta_en_la_ruta_menu(): void
    {
        $request = Request::create('/dashboard/menu');
        $ruta = new Route(['GET'], '/dashboard/menu', fn () => null);
        $ruta->name('menu');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('carta', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_es_inventario_en_su_ruta(): void
    {
        $request = Request::create('/dashboard/inventario');
        $ruta = new Route(['GET'], '/dashboard/inventario', fn () => null);
        $ruta->name('inventario');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('inventario', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_es_eventos_en_su_ruta(): void
    {
        $request = Request::create('/dashboard/eventos');
        $ruta = new Route(['GET'], '/dashboard/eventos', fn () => null);
        $ruta->name('eventos');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('eventos', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_es_eventos_en_el_detalle(): void
    {
        $request = Request::create('/dashboard/eventos/1');
        $ruta = new Route(['GET'], '/dashboard/eventos/{evento}', fn () => null);
        $ruta->name('eventos.detalle');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('eventos', $this->servicio($request)->seccionActiva());
    }

    public function test_seccion_activa_queda_vacia_fuera_del_dashboard(): void
    {
        $this->assertSame('', $this->servicio(Request::create('/otra'))->seccionActiva());
    }

    private function servicio(Request $request, bool $puedeInventario = true): NavegacionDashboardService
    {
        $request->setUserResolver(fn () => new Usuario([
            'primer_nombre' => 'Ana',
            'primer_apellido' => 'Perez',
            'correo' => 'ana@olimpia.com',
        ]));

        $autorizacion = Mockery::mock(AutorizacionInventarioServiceInterface::class);
        $autorizacion->shouldReceive('puedeAcceder')->andReturn($puedeInventario);

        return new NavegacionDashboardService($request, $autorizacion);
    }
}
