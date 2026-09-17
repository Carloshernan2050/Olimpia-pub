<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Contracts\Repositories\DetallePedidoRepositoryInterface;
use App\Contracts\Repositories\EventoRepositoryInterface;
use App\Contracts\Repositories\GrupoMesaRepositoryInterface;
use App\Contracts\Repositories\HistorialRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\PedidoRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Contracts\Services\CatalogoEventosServiceInterface;
use App\Contracts\Services\CatalogoInventarioServiceInterface;
use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\Contracts\Services\CatalogoMesasServiceInterface;
use App\Contracts\Services\CatalogoPromocionesServiceInterface;
use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\Contracts\Services\DatabaseInstallerInterface;
use App\Contracts\Services\EnlacePedidoMesaInterface;
use App\Contracts\Services\GeneradorCodigoQrInterface;
use App\Contracts\Services\GestionEventosServiceInterface;
use App\Contracts\Services\GestionGruposMesaServiceInterface;
use App\Contracts\Services\GestionInventarioServiceInterface;
use App\Contracts\Services\GestionMesasServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\Contracts\Services\GestionPromocionesServiceInterface;
use App\Contracts\Services\MenuPedidoMesaServiceInterface;
use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\Repositories\EloquentCategoriaRepository;
use App\Repositories\EloquentCodigoQrRepository;
use App\Repositories\EloquentContenidoInicioRepository;
use App\Repositories\EloquentDetallePedidoRepository;
use App\Repositories\EloquentEventoRepository;
use App\Repositories\EloquentGrupoMesaRepository;
use App\Repositories\EloquentHistorialRepository;
use App\Repositories\EloquentMesaRepository;
use App\Repositories\EloquentMovimientoInventarioRepository;
use App\Repositories\EloquentPedidoRepository;
use App\Repositories\EloquentProductoRepository;
use App\Repositories\EloquentPromocionRepository;
use App\Repositories\EloquentRolRepository;
use App\Repositories\EloquentUsuarioRepository;
use App\Services\AlmacenamientoImagenEvento;
use App\Services\AlmacenamientoImagenProducto;
use App\Services\AlmacenamientoImagenPromocion;
use App\Services\AutenticacionService;
use App\Services\AutorizacionInventarioService;
use App\Services\CatalogoEventosService;
use App\Services\CatalogoInventarioService;
use App\Services\CatalogoMenuService;
use App\Services\CatalogoMesasService;
use App\Services\CatalogoPromocionesService;
use App\Services\ContenidoInicioService;
use App\Services\DatabaseInstaller;
use App\Services\EnlacePedidoMesa;
use App\Services\GeneradorCodigoQrSvg;
use App\Services\GestionEventosService;
use App\Services\GestionGruposMesaService;
use App\Services\GestionInventarioService;
use App\Services\GestionMesasService;
use App\Services\GestionPedidosService;
use App\Services\GestionPromocionesService;
use App\Services\MenuPedidoMesaService;
use App\Services\NavegacionDashboardService;
use App\View\Composers\DashboardComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios, repositorios y el guardia de autenticación.
     */
    public function register(): void
    {
        foreach ([
            DatabaseInstallerInterface::class => DatabaseInstaller::class,
            AutenticacionServiceInterface::class => AutenticacionService::class,
            AutorizacionInventarioServiceInterface::class => AutorizacionInventarioService::class,
            ContenidoInicioServiceInterface::class => ContenidoInicioService::class,
            CatalogoPromocionesServiceInterface::class => CatalogoPromocionesService::class,
            CatalogoEventosServiceInterface::class => CatalogoEventosService::class,
            CatalogoInventarioServiceInterface::class => CatalogoInventarioService::class,
            CatalogoMenuServiceInterface::class => CatalogoMenuService::class,
            CatalogoMesasServiceInterface::class => CatalogoMesasService::class,
            EnlacePedidoMesaInterface::class => EnlacePedidoMesa::class,
            GeneradorCodigoQrInterface::class => GeneradorCodigoQrSvg::class,
            GestionPromocionesServiceInterface::class => GestionPromocionesService::class,
            GestionEventosServiceInterface::class => GestionEventosService::class,
            GestionInventarioServiceInterface::class => GestionInventarioService::class,
            GestionMesasServiceInterface::class => GestionMesasService::class,
            GestionGruposMesaServiceInterface::class => GestionGruposMesaService::class,
            GestionPedidosServiceInterface::class => GestionPedidosService::class,
            MenuPedidoMesaServiceInterface::class => MenuPedidoMesaService::class,
            NavegacionDashboardServiceInterface::class => NavegacionDashboardService::class,
            RolRepositoryInterface::class => EloquentRolRepository::class,
            UsuarioRepositoryInterface::class => EloquentUsuarioRepository::class,
            CategoriaRepositoryInterface::class => EloquentCategoriaRepository::class,
            CodigoQrRepositoryInterface::class => EloquentCodigoQrRepository::class,
            MesaRepositoryInterface::class => EloquentMesaRepository::class,
            GrupoMesaRepositoryInterface::class => EloquentGrupoMesaRepository::class,
            PedidoRepositoryInterface::class => EloquentPedidoRepository::class,
            HistorialRepositoryInterface::class => EloquentHistorialRepository::class,
            DetallePedidoRepositoryInterface::class => EloquentDetallePedidoRepository::class,
            ProductoRepositoryInterface::class => EloquentProductoRepository::class,
            PromocionRepositoryInterface::class => EloquentPromocionRepository::class,
            EventoRepositoryInterface::class => EloquentEventoRepository::class,
            MovimientoInventarioRepositoryInterface::class => EloquentMovimientoInventarioRepository::class,
            ContenidoInicioRepositoryInterface::class => EloquentContenidoInicioRepository::class,
        ] as $abstracto => $concreto) {
            $this->app->bind($abstracto, $concreto);
        }

        $this->app->when(AlmacenamientoImagenPromocion::class)
            ->needs(Filesystem::class)
            ->give(fn () => Storage::disk('public'));

        $this->app->when(AlmacenamientoImagenEvento::class)
            ->needs(Filesystem::class)
            ->give(fn () => Storage::disk('public'));

        $this->app->when(AlmacenamientoImagenProducto::class)
            ->needs(Filesystem::class)
            ->give(fn () => Storage::disk('public'));

        $this->app->when(GestionPromocionesService::class)
            ->needs(AlmacenamientoImagenPublicaInterface::class)
            ->give(AlmacenamientoImagenPromocion::class);

        $this->app->when(GestionEventosService::class)
            ->needs(AlmacenamientoImagenPublicaInterface::class)
            ->give(AlmacenamientoImagenEvento::class);

        $this->app->when(GestionInventarioService::class)
            ->needs(AlmacenamientoImagenPublicaInterface::class)
            ->give(AlmacenamientoImagenProducto::class);

        $this->app->bind(StatefulGuard::class, function () {
            return Auth::guard('web');
        });
    }

    /**
     * Configura el layout del dashboard y los limitadores de tasa.
     */
    public function boot(): void
    {
        View::composer('layouts.dashboard', DashboardComposer::class);

        RateLimiter::for('registro', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('inicio-sesion', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->ip().'|'.strtolower((string) $request->input('correo'))
            );
        });

        RateLimiter::for('pedidos-mesa', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
