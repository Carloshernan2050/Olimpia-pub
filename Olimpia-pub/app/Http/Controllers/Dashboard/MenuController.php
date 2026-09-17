<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarCatalogoMenuRequest;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Inyecta el catálogo del menú.
     */
    public function __construct(
        private readonly CatalogoMenuServiceInterface $catalogoMenu,
    ) {}

    /**
     * Muestra la carta con los productos del inventario.
     */
    public function mostrar(ConsultarCatalogoMenuRequest $request): View
    {
        $filtro = $request->filtro();

        return view('dashboard.menu', [
            'catalogo' => $this->catalogoMenu->obtenerCatalogo($filtro),
            'filtro' => $filtro,
        ]);
    }
}
