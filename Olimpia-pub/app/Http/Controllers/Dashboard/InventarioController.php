<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\CatalogoInventarioServiceInterface;
use App\Contracts\Services\GestionInventarioServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarInventarioRequest;
use App\Http\Requests\GuardarMovimientoInventarioRequest;
use App\Http\Requests\GuardarProductoInventarioRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventarioController extends Controller
{
    /**
     * Inyecta el catálogo y la gestión de inventario.
     */
    public function __construct(
        private readonly CatalogoInventarioServiceInterface $catalogoInventario,
        private readonly GestionInventarioServiceInterface $gestionInventario,
    ) {}

    /**
     * Muestra el catálogo, el filtro y el modal de inventario.
     */
    public function mostrar(ConsultarInventarioRequest $request): View
    {
        $filtro = $request->filtro();
        $idEdicion = $request->idEdicion();
        $idVer = $request->idVer();

        return view('dashboard.inventario', [
            'catalogo' => $this->catalogoInventario->obtenerCatalogo($filtro),
            'filtro' => $filtro,
            'movimientosGestion' => $this->gestionInventario->listar(),
            'movimientoEditar' => $idEdicion === null ? null : $this->gestionInventario->buscar($idEdicion),
            'productoVer' => $idVer === null ? null : $this->gestionInventario->buscarProducto($idVer),
            'movimientosProducto' => $idVer === null ? [] : $this->gestionInventario->listarDeProducto($idVer),
            'idProductoPrefill' => $request->idProductoPrefill(),
            'formularioMovimiento' => $this->debeMostrarFormularioMovimiento($request),
            'abrirModal' => $request->debeAbrirModal(),
        ]);
    }

    /**
     * Da de alta un producto en el inventario.
     */
    public function guardarProducto(GuardarProductoInventarioRequest $request): RedirectResponse
    {
        $this->gestionInventario->crearProducto(
            $request->datos(),
            (int) $request->user()->getAuthIdentifier(),
        );

        return $this->redirigirAlCatalogo('Producto creado correctamente.');
    }

    /**
     * Registra un movimiento de inventario.
     */
    public function guardar(GuardarMovimientoInventarioRequest $request): RedirectResponse
    {
        $this->gestionInventario->crear(
            $request->datos(),
            (int) $request->user()->getAuthIdentifier(),
        );

        return $this->redirigirAlCatalogo('Movimiento registrado correctamente.');
    }

    /**
     * Actualiza un movimiento de inventario.
     */
    public function actualizar(GuardarMovimientoInventarioRequest $request, int $movimiento): RedirectResponse
    {
        $this->gestionInventario->actualizar($movimiento, $request->datos());

        return $this->redirigirAlCatalogo('Movimiento actualizado correctamente.');
    }

    /**
     * Elimina un movimiento de inventario.
     */
    public function eliminar(int $movimiento): RedirectResponse
    {
        $this->gestionInventario->eliminar($movimiento);

        return $this->redirigirAlCatalogo('Movimiento eliminado correctamente.');
    }

    /**
     * Elimina un producto del inventario.
     */
    public function eliminarProducto(int $producto): RedirectResponse
    {
        $this->gestionInventario->eliminarProducto($producto);

        return $this->redirigirAlCatalogo('Producto eliminado del inventario.');
    }

    /**
     * El modal de movimiento se abre al editar, prellenar o si falló ese formulario.
     */
    private function debeMostrarFormularioMovimiento(ConsultarInventarioRequest $request): bool
    {
        return $request->idEdicion() !== null
            || $request->idProductoPrefill() !== null
            || $request->old('formulario') === 'movimiento';
    }

    /**
     * Vuelve al catálogo con el aviso de éxito.
     */
    private function redirigirAlCatalogo(string $mensaje): RedirectResponse
    {
        return redirect()->route('inventario')->with('exito', $mensaje);
    }
}
