<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\Contracts\Services\DatabaseInstallerInterface;
use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\Repositories\EloquentCategoriaRepository;
use App\Repositories\EloquentCodigoQrRepository;
use App\Repositories\EloquentContenidoInicioRepository;
use App\Repositories\EloquentMesaRepository;
use App\Repositories\EloquentProductoRepository;
use App\Repositories\EloquentRolRepository;
use App\Repositories\EloquentUsuarioRepository;
use App\Services\AutenticacionService;
use App\Services\ContenidoInicioService;
use App\Services\DatabaseInstaller;
use App\Services\NavegacionDashboardService;
use App\View\Composers\DashboardComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
            ContenidoInicioServiceInterface::class => ContenidoInicioService::class,
            NavegacionDashboardServiceInterface::class => NavegacionDashboardService::class,
            RolRepositoryInterface::class => EloquentRolRepository::class,
            UsuarioRepositoryInterface::class => EloquentUsuarioRepository::class,
            CategoriaRepositoryInterface::class => EloquentCategoriaRepository::class,
            CodigoQrRepositoryInterface::class => EloquentCodigoQrRepository::class,
            MesaRepositoryInterface::class => EloquentMesaRepository::class,
            ProductoRepositoryInterface::class => EloquentProductoRepository::class,
            ContenidoInicioRepositoryInterface::class => EloquentContenidoInicioRepository::class,
        ] as $abstracto => $concreto) {
            $this->app->bind($abstracto, $concreto);
        }

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
