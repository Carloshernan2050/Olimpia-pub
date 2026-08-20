<?php

namespace App\View\Composers;

use App\Contracts\Services\NavegacionDashboardServiceInterface;
use App\Models\Usuario;
use Illuminate\View\View;

class DashboardComposer
{
    /**
     * Inyecta la navegación del dashboard.
     */
    public function __construct(
        private readonly NavegacionDashboardServiceInterface $navegacion,
    ) {}

    /**
     * Comparte header, nav y usuario con el layout autenticado.
     */
    public function compose(View $view): void
    {
        $usuario = auth()->user();

        $view->with([
            'itemsNavegacion' => $this->navegacion->items(),
            'accionesCabecera' => $this->navegacion->accionesCabecera(),
            'seccionActiva' => $this->navegacion->seccionActiva(),
            'nombreUsuario' => $usuario instanceof Usuario
                ? (string) $usuario->primer_nombre
                : '',
        ]);
    }
}
