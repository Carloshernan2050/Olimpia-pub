<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\MovimientoInventarioRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Repositories\PromocionRepositoryInterface;
use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AlmacenamientoImagenPromocionInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\Contracts\Services\AutorizacionInventarioServiceInterface;
use App\Contracts\Services\CatalogoInventarioServiceInterface;
use App\Contracts\Services\CatalogoPromocionesServiceInterface;
use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\Contracts\Services\DatabaseInstallerInterface;
use App\Contracts\Services\GestionInventarioServiceInterface;
use App\Contracts\Services\GestionPromocionesServiceInterface;
use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\Repositories\EloquentCategoriaRepository;
use App\Repositories\EloquentCodigoQrRepository;
use App\Repositories\EloquentContenidoInicioRepository;
use App\Repositories\EloquentMesaRepository;
use App\Repositories\EloquentMovimientoInventarioRepository;
use App\Repositories\EloquentProductoRepository;
use App\Repositories\EloquentPromocionRepository;
use App\Repositories\EloquentRolRepository;
use App\Repositories\EloquentUsuarioRepository;
use App\Services\AlmacenamientoImagenPromocion;
use App\Services\AutenticacionService;
use App\Services\AutorizacionInventarioService;
use App\Services\CatalogoInventarioService;
use App\Services\CatalogoPromocionesService;
use App\Services\ContenidoInicioService;
use App\Services\DatabaseInstaller;
use App\Services\GestionInventarioService;
use App\Services\GestionPromocionesService;
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
            CatalogoInventarioServiceInterface::class => CatalogoInventarioService::class,
            GestionPromocionesServiceInterface::class => GestionPromocionesService::class,
            GestionInventarioServiceInterface::class => GestionInventarioService::class,
            NavegacionDashboardServiceInterface::class => NavegacionDashboardService::class,
            RolRepositoryInterface::class => EloquentRolRepository::class,
            UsuarioRepositoryInterface::class => EloquentUsuarioRepository::class,
            CategoriaRepositoryInterface::class => EloquentCategoriaRepository::class,
            CodigoQrRepositoryInterface::class => EloquentCodigoQrRepository::class,
            MesaRepositoryInterface::class => EloquentMesaRepository::class,
            ProductoRepositoryInterface::class => EloquentProductoRepository::class,
            PromocionRepositoryInterface::class => EloquentPromocionRepository::class,
            MovimientoInventarioRepositoryInterface::class => EloquentMovimientoInventarioRepository::class,
            ContenidoInicioRepositoryInterface::class => EloquentContenidoInicioRepository::class,
            AlmacenamientoImagenPromocionInterface::class => AlmacenamientoImagenPromocion::class,
        ] as $abstracto => $concreto) {
            $this->app->bind($abstracto, $concreto);
        }

        $this->app->when(AlmacenamientoImagenPromocion::class)
            ->needs(Filesystem::class)
            ->give(fn () => Storage::disk('public'));

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
    }
}
