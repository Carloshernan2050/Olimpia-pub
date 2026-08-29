<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\CatalogoPromocionesServiceInterface;
use App\Contracts\Services\GestionPromocionesServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarCatalogoPromocionesRequest;
use App\Http\Requests\GuardarPromocionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class PromocionController extends Controller
{
    /**
     * Inyecta el catálogo y la gestión de promociones.
     */
    public function __construct(
        private readonly CatalogoPromocionesServiceInterface $catalogoPromociones,
        private readonly GestionPromocionesServiceInterface $gestionPromociones,
    ) {}

    /**
     * Muestra el catálogo, el filtro y el modal de CRUD.
     */
    public function mostrar(ConsultarCatalogoPromocionesRequest $request): View
    {
        $filtro = $request->filtro();
        $idEdicion = $request->idEdicion();

        return view('dashboard.promociones', [
            'catalogo' => $this->catalogoPromociones->obtenerCatalogo($filtro),
            'filtro' => $filtro,
            'promocionesGestion' => $this->gestionPromociones->listar(),
            'promocionEditar' => $idEdicion === null ? null : $this->gestionPromociones->buscar($idEdicion),
            'abrirModal' => $request->debeAbrirModal(),
        ]);
    }

    /**
     * Crea una promoción.
     */
    public function guardar(GuardarPromocionRequest $request): RedirectResponse
    {
        $this->gestionPromociones->crear(
            $request->datos(),
            (int) $request->user()->getAuthIdentifier(),
            $this->imagenSubida($request),
        );

        return $this->redirigirAlCatalogo('Promoción creada correctamente.');
    }

    /**
     * Actualiza una promoción.
     */
    public function actualizar(GuardarPromocionRequest $request, int $promocion): RedirectResponse
    {
        $this->gestionPromociones->actualizar(
            $promocion,
            $request->datos(),
            $this->imagenSubida($request),
        );

        return $this->redirigirAlCatalogo('Promoción actualizada correctamente.');
    }

    /**
     * Elimina una promoción.
     */
    public function eliminar(int $promocion): RedirectResponse
    {
        $this->gestionPromociones->eliminar($promocion);

        return $this->redirigirAlCatalogo('Promoción eliminada correctamente.');
    }

    /**
     * Vuelve al catálogo con el aviso de éxito.
     */
    private function redirigirAlCatalogo(string $mensaje): RedirectResponse
    {
        return redirect()->route('promociones')->with('exito', $mensaje);
    }

    /**
     * Archivo de imagen validado, si el usuario envió uno.
     */
    private function imagenSubida(GuardarPromocionRequest $request): ?UploadedFile
    {
        $imagen = $request->file('imagen');

        return $imagen instanceof UploadedFile ? $imagen : null;
    }
}
