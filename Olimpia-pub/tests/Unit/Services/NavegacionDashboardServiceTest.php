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
        $promociones = $items[2];

        $this->assertCount(9, $items);
        $this->assertSame('inicio', $inicio->clave);
        $this->assertSame('dashboard', $inicio->ruta);
        $this->assertTrue($inicio->estaDisponible());
        $this->assertSame('promociones', $promociones->clave);
        $this->assertSame('promociones', $promociones->ruta);
        $this->assertTrue($promociones->estaDisponible());
        $this->assertSame('inventario', $items[3]->clave);
        $this->assertSame('inventario', $items[3]->ruta);
        $this->assertTrue($items[3]->estaDisponible());
        $this->assertCount(3, array_filter($items, fn ($item) => $item->estaDisponible()));
    }

    public function test_sin_permiso_no_muestra_inventario(): void
    {
        $claves = array_map(
            fn ($item) => $item->clave,
            $this->servicio(Request::create('/dashboard'), false)->items(),
        );

        $this->assertCount(8, $claves);
        $this->assertNotContains('inventario', $claves);
        $this->assertCount(2, array_filter(
            $this->servicio(Request::create('/dashboard'), false)->items(),
            fn ($item) => $item->estaDisponible(),
        ));
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

    public function test_seccion_activa_es_inventario_en_su_ruta(): void
    {
        $request = Request::create('/dashboard/inventario');
        $ruta = new Route(['GET'], '/dashboard/inventario', fn () => null);
        $ruta->name('inventario');
        $request->setRouteResolver(fn () => $ruta);

        $this->assertSame('inventario', $this->servicio($request)->seccionActiva());
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
