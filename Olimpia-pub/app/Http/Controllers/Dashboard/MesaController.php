<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\Services\CatalogoMesasServiceInterface;
use App\Contracts\Services\GestionGruposMesaServiceInterface;
use App\Contracts\Services\GestionMesasServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultarMesasRequest;
use App\Http\Requests\GuardarGrupoMesaRequest;
use App\Http\Requests\GuardarMesaRequest;
use App\Http\Requests\LiberarGruposMesaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MesaController extends Controller
{
    /**
     * Inyecta el catálogo, las mesas y los grupos.
     */
    public function __construct(
        private readonly CatalogoMesasServiceInterface $catalogoMesas,
        private readonly GestionMesasServiceInterface $gestionMesas,
        private readonly GestionGruposMesaServiceInterface $gestionGrupos,
        private readonly GestionPedidosServiceInterface $gestionPedidos,
    ) {}

    /**
     * Muestra la lista de mesas, el filtro y el modal.
     */
    public function mostrar(ConsultarMesasRequest $request): View
    {
        $filtro = $request->filtro();
        $idEdicion = $request->idEdicion();
        $idVer = $request->idVer();
        $idPedido = $request->idPedido();
        $idVerGrupo = $request->idVerGrupo();
        $idEdicionGrupo = $request->idEdicionGrupo();
        $idPedidoGrupo = $request->idPedidoGrupo();
        $formularioGrupo = $request->old('formulario') === 'grupo';
        $formularioLiberar = $request->old('formulario') === 'liberar';

        return view('dashboard.mesas', [
            'catalogo' => $this->catalogoMesas->obtenerCatalogo($filtro),
            'filtro' => $filtro,
            'mesaVer' => $idVer === null ? null : $this->gestionMesas->buscar($idVer),
            'mesaEditar' => $idEdicion === null ? null : $this->gestionMesas->buscar($idEdicion),
            'pedidoVer' => $idPedido === null ? null : $this->gestionMesas->buscar($idPedido),
            'grupoVer' => $idVerGrupo === null ? null : $this->gestionGrupos->buscar($idVerGrupo),
            'grupoEditar' => $idEdicionGrupo === null ? null : $this->gestionGrupos->buscar($idEdicionGrupo),
            'pedidoGrupoVer' => $idPedidoGrupo === null ? null : $this->gestionGrupos->buscar($idPedidoGrupo),
            'unir' => $request->debeUnir() || ($formularioGrupo && $idEdicionGrupo === null),
            'liberar' => $request->debeLiberar() || $formularioLiberar,
            'abrirModal' => $request->debeAbrirModal()
                || $request->old('formulario') === 'mesa'
                || $formularioGrupo
                || $formularioLiberar,
        ]);
    }

    /**
     * Crea una mesa con su código QR.
     */
    public function guardar(GuardarMesaRequest $request): RedirectResponse
    {
        $this->gestionMesas->crear($request->datos());

        return $this->redirigirAlCatalogo('Mesa creada correctamente.');
    }

    /**
     * Actualiza una mesa existente.
     */
    public function actualizar(GuardarMesaRequest $request, int $mesa): RedirectResponse
    {
        $this->gestionMesas->actualizar($mesa, $request->datos());

        return $this->redirigirAlCatalogo('Mesa actualizada correctamente.');
    }

    /**
     * Elimina una mesa si no tiene pedidos.
     */
    public function eliminar(int $mesa): RedirectResponse
    {
        $this->gestionMesas->eliminar($mesa);

        return $this->redirigirAlCatalogo('Mesa eliminada correctamente.');
    }

    /**
     * Une las mesas seleccionadas en un grupo.
     */
    public function guardarGrupo(GuardarGrupoMesaRequest $request): RedirectResponse
    {
        $this->gestionGrupos->unir($request->datos());

        return $this->redirigirAlCatalogo('Mesas unidas correctamente.');
    }

    /**
     * Actualiza las mesas de un grupo.
     */
    public function actualizarGrupo(GuardarGrupoMesaRequest $request, int $grupo): RedirectResponse
    {
        $this->gestionGrupos->actualizar($grupo, $request->datos());

        return $this->redirigirAlCatalogo('Grupo actualizado correctamente.');
    }

    /**
     * Libera las uniones seleccionadas.
     */
    public function liberarGrupos(LiberarGruposMesaRequest $request): RedirectResponse
    {
        $this->gestionGrupos->liberar($request->datos());

        return $this->redirigirAlCatalogo('Mesas liberadas correctamente.');
    }

    /**
     * Separa el grupo; las mesas siguen existiendo.
     */
    public function eliminarGrupo(int $grupo): RedirectResponse
    {
        $this->gestionGrupos->separar($grupo);

        return $this->redirigirAlCatalogo('Grupo separado correctamente.');
    }

    /**
     * Cierra el pedido activo de la mesa y lo registra en el historial.
     */
    public function terminarPedido(Request $request, int $mesa): RedirectResponse
    {
        $this->gestionPedidos->terminar($mesa, (int) $request->user()->getAuthIdentifier());

        return $this->redirigirAlCatalogo('Pedido terminado correctamente.');
    }

    /**
     * Cierra el pedido activo del grupo y lo registra en el historial.
     */
    public function terminarPedidoGrupo(Request $request, int $grupo): RedirectResponse
    {
        $this->gestionGrupos->terminarPedido($grupo, (int) $request->user()->getAuthIdentifier());

        return $this->redirigirAlCatalogo('Pedido terminado correctamente.');
    }

    /**
     * Vuelve al catálogo con el aviso de éxito.
     */
    private function redirigirAlCatalogo(string $mensaje): RedirectResponse
    {
        return redirect()->route('mesas')->with('exito', $mensaje);
    }
}
