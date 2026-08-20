<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Inyecta el servicio de la portada de Home.
     */
    public function __construct(
        private readonly ContenidoInicioServiceInterface $contenidoInicio,
    ) {}

    /**
     * Muestra el dashboard principal en la sección Home.
     */
    public function mostrar(): View
    {
        return view('dashboard.inicio', [
            'portada' => $this->contenidoInicio->obtenerPortada(),
        ]);
    }
}
