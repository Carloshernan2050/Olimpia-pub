<?php

namespace App\Providers;

use App\Contracts\Repositories\CategoriaRepositoryInterface;
use App\Contracts\Repositories\CodigoQrRepositoryInterface;
use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Repositories\ProductoRepositoryInterface;
use App\Contracts\Repositories\RolRepositoryInterface;
use App\Contracts\Repositories\UsuarioRepositoryInterface;
use App\Contracts\Services\AutenticacionServiceInterface;
use App\Contracts\Services\DatabaseInstallerInterface;
use App\Repositories\EloquentCategoriaRepository;
use App\Repositories\EloquentCodigoQrRepository;
use App\Repositories\EloquentMesaRepository;
use App\Repositories\EloquentProductoRepository;
use App\Repositories\EloquentRolRepository;
use App\Repositories\EloquentUsuarioRepository;
use App\Services\AutenticacionService;
use App\Services\DatabaseInstaller;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DatabaseInstallerInterface::class, DatabaseInstaller::class);
        $this->app->bind(AutenticacionServiceInterface::class, AutenticacionService::class);
        $this->app->bind(RolRepositoryInterface::class, EloquentRolRepository::class);
        $this->app->bind(UsuarioRepositoryInterface::class, EloquentUsuarioRepository::class);
        $this->app->bind(CategoriaRepositoryInterface::class, EloquentCategoriaRepository::class);
        $this->app->bind(CodigoQrRepositoryInterface::class, EloquentCodigoQrRepository::class);
        $this->app->bind(MesaRepositoryInterface::class, EloquentMesaRepository::class);
        $this->app->bind(ProductoRepositoryInterface::class, EloquentProductoRepository::class);

        $this->app->bind(StatefulGuard::class, function () {
            return Auth::guard('web');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
