<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\CatalogoEventosServiceInterface;
use App\Contracts\Services\GestionEventosServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarCatalogoEventosRequest;
use App\Http\Requests\GuardarEventoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventoController extends Controller
{
    /**
     * Inyecta el catálogo y la gestión de eventos.
     */
    public function __construct(
        private readonly CatalogoEventosServiceInterface $catalogoEventos,
        private readonly GestionEventosServiceInterface $gestionEventos,
    ) {}

    /**
     * Muestra el catálogo, el filtro y los modales.
     */
    public function mostrar(ConsultarCatalogoEventosRequest $request): View
    {
        $filtro = $request->filtro();
        $idEdicion = $request->idEdicion();
        $idVer = $request->idVer();

        return view('dashboard.eventos', [
            'catalogo' => $this->catalogoEventos->obtenerCatalogo($filtro),
            'filtro' => $filtro,
            'eventosGestion' => $this->gestionEventos->listar(),
            'eventoEditar' => $idEdicion === null ? null : $this->gestionEventos->buscar($idEdicion),
            'eventoVer' => $idVer === null ? null : $this->catalogoEventos->obtenerDetalle($idVer),
            'abrirModal' => $request->debeAbrirModal(),
        ]);
    }

    /**
     * Crea un evento.
     */
    public function guardar(GuardarEventoRequest $request): RedirectResponse
    {
        $this->gestionEventos->crear(
            $request->datos(),
            (int) $request->user()->getAuthIdentifier(),
            $request->imagenSubida(),
        );

        return $this->redirigirAlCatalogo('Evento creado correctamente.');
    }

    /**
     * Actualiza un evento.
     */
    public function actualizar(GuardarEventoRequest $request, int $evento): RedirectResponse
    {
        $this->gestionEventos->actualizar(
            $evento,
            $request->datos(),
            $request->imagenSubida(),
        );

        return $this->redirigirAlCatalogo('Evento actualizado correctamente.');
    }

    /**
     * Elimina un evento.
     */
    public function eliminar(int $evento): RedirectResponse
    {
        $this->gestionEventos->eliminar($evento);

        return $this->redirigirAlCatalogo('Evento eliminado correctamente.');
    }

    /**
     * Abre el catálogo con el detalle centrado.
     */
    public function detalle(int $evento): RedirectResponse
    {
        return redirect()->route('eventos', ['ver' => $evento]);
    }

    /**
     * Vuelve al catálogo con el aviso de éxito.
     */
    private function redirigirAlCatalogo(string $mensaje): RedirectResponse
    {
        return redirect()->route('eventos')->with('exito', $mensaje);
    }
}
