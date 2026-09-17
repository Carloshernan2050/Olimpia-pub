<?php

namespace App\Contracts\Services;

use App\DTOs\Dashboard\FiltroMenuDatos;
use App\DTOs\Dashboard\GuardarPedidoMesaDatos;
use App\DTOs\Dashboard\PedidoMesaDatos;
use App\DTOs\Pedido\MesaPedidoPublicoDatos;

interface MenuPedidoMesaServiceInterface
{
    /**
     * Menú público de la mesa identificada por su código QR.
     */
    public function obtener(string $codigo, ?FiltroMenuDatos $filtro = null): MesaPedidoPublicoDatos;

    /**
     * Registra el pedido del cliente en la mesa del código QR.
     */
    public function pedir(string $codigo, GuardarPedidoMesaDatos $datos): PedidoMesaDatos;
}
