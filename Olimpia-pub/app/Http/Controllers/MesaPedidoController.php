<?php

namespace App\Http\Controllers;

use App\Contracts\Services\MenuPedidoMesaServiceInterface;
use App\Http\Requests\ConsultarMenuPedidoRequest;
use App\Http\Requests\GuardarPedidoMesaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MesaPedidoController extends Controller
{
    /**
     * Inyecta el menú público de la mesa.
     */
    public function __construct(
        private readonly MenuPedidoMesaServiceInterface $menuPedido,
    ) {}

    /**
     * Muestra la carta para pedir desde el QR de la mesa.
     */
    public function mostrar(ConsultarMenuPedidoRequest $request, string $codigo): View
    {
        $filtro = $request->filtro();

        return view('pedido.menu', [
            'mesa' => $this->menuPedido->obtener($codigo, $filtro),
            'filtro' => $filtro,
        ]);
    }

    /**
     * Envía el pedido del cliente a la mesa del QR.
     */
    public function pedir(GuardarPedidoMesaRequest $request, string $codigo): RedirectResponse
    {
        $this->menuPedido->pedir($codigo, $request->datos());

        return redirect()
            ->route('mesa.menu', ['codigo' => $codigo])
            ->with('exito', 'Pedido enviado a la mesa.');
    }
}
