<?php

namespace App\Services;

use App\Contracts\Repositories\MesaRepositoryInterface;
use App\Contracts\Services\CatalogoMenuServiceInterface;
use App\Contracts\Services\GestionPedidosServiceInterface;
use App\Contracts\Services\MenuPedidoMesaServiceInterface;
use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;
use App\DTOs\Pedido\MesaPedidoPublicoDatos;
use App\Exceptions\Mesa\MesaNoEncontradaException;
use App\Models\Mesa;

class MenuPedidoMesaService implements MenuPedidoMesaServiceInterface
{
    /**
     * Inyecta mesas, el catálogo y el registro de pedidos.
     */
    public function __construct(
        private readonly MesaRepositoryInterface $mesaRepository,
        private readonly CatalogoMenuServiceInterface $catalogoMenu,
        private readonly GestionPedidosServiceInterface $gestionPedidos,
    ) {}

    /**
     * Arma la carta pública de la mesa del código QR.
     */
    public function obtener(string $codigo, ?FiltroMenuDatos $filtro = null): MesaPedidoPublicoDatos
    {
        $mesa = $this->mesaDeCodigo($codigo);
        $pedido = $mesa->pedidoActivo;

        return new MesaPedidoPublicoDatos(
            (int) $mesa->id_mesa,
            (int) $mesa->numero_mesa,
            (string) ($mesa->codigoQr?->codigo_qr ?? $codigo),
            $this->catalogoMenu->obtenerCatalogo($filtro),
            $pedido === null ? null : PedidoMesaDatos::fromModel($pedido),
        );
    }

    /**
     * Crea el pedido o suma productos al que ya está activo.
     */
    public function pedir(string $codigo, GuardarPedidoMesaDatos $datos): PedidoMesaDatos
    {
        $mesa = $this->mesaDeCodigo($codigo);

        return $this->gestionPedidos->registrar(new GuardarPedidoMesaDatos(
            (int) $mesa->id_mesa,
            $datos->lineas,
        ));
    }

    private function mesaDeCodigo(string $codigo): Mesa
    {
        $mesa = $this->mesaRepository->findByCodigoQr($codigo);

        if ($mesa === null) {
            throw new MesaNoEncontradaException;
        }

        return $mesa;
    }
}
